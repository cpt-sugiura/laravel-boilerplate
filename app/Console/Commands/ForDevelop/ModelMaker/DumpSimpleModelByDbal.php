<?php

namespace App\Console\Commands\ForDevelop\ModelMaker;

use App\Console\BaseCommand;
use Arr;
use DB;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RuntimeException;
use Str;
use Symfony\Component\Console\Input\InputOption;
use View;

class DumpSimpleModelByDbal extends BaseCommand
{
    protected $name = "dev:dump-simple-model-by-dbal";
    protected $description = 'データベース中のテーブルを元にEloquentモデルを生成';

    protected function getOptions(): array
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'テーブルに対応する全モデルを生成'],
            ['table', 't', InputOption::VALUE_OPTIONAL, 'モデル生成元のテーブルを指定'],
        ];
    }

    private AbstractSchemaManager $schemaManager;
    public const DB_TYPE_TO_CAST_MAP = [
        'boolean'    => 'boolean',
        'tinyint'    => 'integer',
        'smallint'   => 'integer',
        'mediumint'  => 'integer',
        'int'        => 'integer',
        'integer'    => 'integer',
        'bigint'     => 'integer',
        'tinytext'   => 'string',
        'mediumtext' => 'string',
        'longtext'   => 'string',
        'text'       => 'string',
        'varchar'    => 'string',
        'string'     => 'string',
        'char'       => 'string',
        'date'       => 'date',
        'datetime'   => 'date',
        'timestamp'  => 'date',
        'time'       => 'string',
        'float'      => 'numeric',
        'double'     => 'numeric',
        'real'       => 'numeric',
        'decimal'    => 'numeric',
        'numeric'    => 'numeric',
        'year'       => 'integer',
        'longblob'   => 'string',
        'blob'       => 'string',
        'mediumblob' => 'string',
        'tinyblob'   => 'string',
        'binary'     => 'string',
        'varbinary'  => 'string',
        'set'        => 'array',
        'geometry'   => null,
    ];

    /**
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        $this->schemaManager = DB::getDoctrineSchemaManager();
        $this->schemaManager->getDatabasePlatform()
            ->registerDoctrineTypeMapping('geometry', 'string');
        $tgtTables = $this->getTgtTables();
        /** @var ForeignKeyConstraint[][][] ForeignKeyConstraint[外部キー制約を持つテーブル名][外部キー制約先のテーブル名][index] 複数の外部キー制約を同じテーブル間で持つ場合を考慮 */
        $foreignKeys = $this->getForeignKeys();
        // 書き出し
        foreach($tgtTables as $table) {
            $this->dumpModel($table, $foreignKeys);
        }

        return 0;
    }

    /**
     * @param  Table                       $table
     * @param  ForeignKeyConstraint[][][]  $foreignKeys  ForeignKeyConstraint[外部キー制約を持つテーブル名][外部キー制約先のテーブル名][index] 複数の外部キー制約を同じテーブル間で持つ場合を考慮
     * @return void
     * @throws Exception
     */
    private function dumpModel(Table $table, array $foreignKeys): void
    {
        $namespace  = 'App\\Models\\Eloquents';
        $className  = ucfirst(Str::camel(Str::singular($table->getName())));
        $tableName  = $table->getName();
        $primaryKey = $this->getPrimaryKey($table);
        $hidden     = $this->getHidden($table);
        $guarded    = $this->getGuarded($table);
        $casts      = $this->getCasts($table);
        $relations  = $this->getRelations($table, $foreignKeys);

        $view = View::file(
            __DIR__ . "/../CodeStab/Model.php.stub.blade.php",
            compact(
                'namespace', 'tableName', 'className', 'primaryKey', 'hidden', 'guarded', 'casts', 'relations'
            )
        );
        file_put_contents(
            app_path('Models/Eloquents/' . $className . ".php"),
            str_replace('%%php%%', '<?php', $view->render())
        );
    }

    /**
     * @param  Table  $table
     * @return string|array|null
     * @throws Exception
     */
    private function getPrimaryKey(Table $table): string|array|null
    {
        if($table->getPrimaryKey() === null) {
            return null;
        }

        if(count($table->getPrimaryKeyColumns()) === 1) {
            $primaryKey = Arr::first($table->getPrimaryKeyColumns())->getName();
        } else {
            $primaryKey = array_map(static fn($c) => $c->getName(), $table->getPrimaryKeyColumns());
        }
        return $primaryKey;
    }

    /**
     * @param  Table  $table
     * @return string[]
     * @throws Exception
     */
    private function getGuarded(Table $table): array
    {
        return array_filter(
            array_map(static fn($c) => $c->getName(), $table->getColumns()),
            static fn(string $name) => in_array($name,
                [
                    ...array_map(
                        static fn($c) => $c->getName(),
                        $table->getPrimaryKey() === null
                            ? []
                            : $table->getPrimaryKeyColumns()
                    ),
                    'password',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                true),
        );
    }

    /**
     * @param  Table  $table
     * @return string[]
     */
    private function getCasts(Table $table): array
    {
        $casts = [];
        foreach($table->getColumns() as $c) {
            $casts[$c->getName()] = self::DB_TYPE_TO_CAST_MAP[$c->getType()->getName()] ?? null;
        }
        return $casts;
    }

    /**
     * @param  Table  $table
     * @param  array  $foreignKeys
     * @return array{retClassName: string, useMethod: string, keyColumnPHPCode: string, methodName: string, tgtClassNamePHPCode: string }
     */
    private function getRelations(Table $table, array $foreignKeys): array
    {
        $relations = [];
        foreach($foreignKeys as $hasIndexTableName => $foreignKeyTgtTableList) {
            if($hasIndexTableName === $table->getName()) {
                foreach($foreignKeyTgtTableList as $fKeyList) {
                    /** @var ForeignKeyConstraint $fKey */
                    foreach($fKeyList as $fKey) {
                        $relations[] = [
                            'retClassName'        => "\\" . BelongsTo::class,
                            'useMethod'           => 'belongsTo',
                            'keyColumnPHPCode'    => count($fKey->getForeignColumns()) === 1
                                ? "'" . Arr::first($fKey->getForeignColumns()) . "'"
                                : "['" . implode("', '", $fKey->getForeignColumns()) . "']",
                            'methodName'          => Str::singular($fKey->getForeignTableName()),
                            'tgtClassNamePHPCode' => '\\App\\Models\\Eloquents\\' . ucfirst(Str::singular($fKey->getForeignTableName())) . "::class"
                        ];
                    }
                }
            } elseif(isset($foreignKeyTgtTableList[$table->getName()]) && !empty($foreignKeyTgtTableList[$table->getName()])) {
                foreach($foreignKeyTgtTableList[$table->getName()] as $fKey) {
                    $relations[] = [
                        'retClassName'        => "\\" . HasMany::class,
                        'useMethod'           => 'hasMany',
                        'keyColumnPHPCode'    => count($fKey->getForeignColumns()) === 1
                            ? "'" . Arr::first($fKey->getForeignColumns()) . "'"
                            : "['" . implode("', '", $fKey->getForeignColumns()) . "']",
                        'methodName'          => Str::camel($hasIndexTableName),
                        'tgtClassNamePHPCode' => '\\App\\Models\\Eloquents\\' . ucfirst(Str::singular(Str::camel($hasIndexTableName))) . "::class"
                    ];
                }
            }
        }
        return $relations;
    }

    /**
     * @param  Table  $table
     * @return string[]
     */
    private function getHidden(Table $table): array
    {
        return array_filter(
            array_map(static fn($c) => $c->getName(), $table->getColumns()),
            static fn(string $name) => in_array($name, ['password', 'auth_token'], true),
        );
    }

    /**
     * 外部キー制約をリストアップ
     * @return ForeignKeyConstraint[][][] ForeignKeyConstraint[外部キー制約を持つテーブル名][外部キー制約先のテーブル名][index] 複数の外部キー制約を同じテーブル間で持つ場合を考慮
     * @throws Exception
     */
    private function getForeignKeys(): array
    {
        $foreignKeys = [];
        foreach($this->schemaManager->listTableNames() as $tableName) {
            $fKeys                   = $this->schemaManager->listTableForeignKeys($tableName);
            $foreignKeys[$tableName] ??= [];
            foreach($fKeys as $f) {
                $foreignKeys[$tableName][$f->getForeignTableName()]   ??= [];
                $foreignKeys[$tableName][$f->getForeignTableName()][] = $f;
            }
        }
        return $foreignKeys;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getTgtTables(): array
    {
        if($this->option('table')) {
            return [$this->schemaManager->listTableDetails($this->option('table'))];
        }

        if($this->option('all')) {
            return $this->schemaManager->listTables();
        }

        throw new RuntimeException('--all オプションを付けるか、-t [テーブル名] でテーブル名を指定する必要があります');
    }
}
