<?php

namespace App\Console\Commands\ForDevelop;

use App\Console\BaseCommand;
use App\Console\Commands\ForDevelop\MakeTestData\DependencySortTable;
use App\Console\Commands\ForDevelop\MakeTestData\MakeTestValue;
use App\Console\Commands\ForDevelop\MakeTestData\ShowColumnRecordModel;
use App\Database\MySqlErrorCode;
use App\Models\Eloquents\BaseEloquent;
use Arr;
use DB;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Table;
use Error;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class MakeTestData extends BaseCommand
{
    protected $name = 'dev:make_test_data';

    protected $description = 'モデルを元に雑にテストデータを生成する';

    /**
     * コマンドオプション
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            new InputOption('all', 'a', InputOption::VALUE_NONE, 'テーブルに対応する全モデルを生成'),
            new InputOption('class', 'c', InputOption::VALUE_OPTIONAL, 'モデル生成元のテーブルクラスを指定。namespace含むクラス名'),
            new InputOption('count', '', InputOption::VALUE_OPTIONAL, '生成する1テーブル毎のテストデータの個数', 100),
            new InputOption('ignore', 'i', InputOption::VALUE_OPTIONAL, '無視するテーブルをカンマ区切り', ''),
        ];
    }

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable
     * @throws \Exception
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('class')) {
            $classNames = [$this->option('class')];
        } elseif ($this->option('all')) {
            $classNames = $this->getClassNamesForAllOption();
        } else {
            $this->error('--all オプションを付けるか、-t [テーブル名] でテーブル名を指定する必要があります');

            return static::FAILURE;
        }

        $n = $this->option('count');
        foreach ($classNames as $className) {
            $this->info($className);
            try {
                $this->main($className, $n);
            } catch (Throwable $e) {
                $this->error('current class: '.$className);
                throw $e;
            }
        }

        return static::SUCCESS;
    }

    /**
     * @param  string     $className
     * @param  int        $n
     * @throws Exception
     * @throws \Exception
     */
    protected function main(string $className, int $n): void
    {
        /** @var BaseEloquent $model */
        $model       = new $className();
        $foreignKeys = $this->getDbal()->listTableForeignKeys($model->getTable());
        /** @var ForeignKeyConstraint[] $foreignKeys キーは外部キー制約がかかっているローカルテーブルカラム */
        $foreignKeys    = collect($foreignKeys)->keyBy(function (ForeignKeyConstraint $fk) use ($model) {
            $fkCols = $fk->getLocalColumns();
            if (count($fkCols) >= 2) {
                $this->warn('複数カラムの外部キー制約には未対応です。table: '.$model->getTable().' fk: '.$fk->getName());
            }

            return Arr::first($fkCols);
        });
        $columns        = $this->getDbal()->listTableColumns($model->getTable());
        $casts          = $model->getCasts();
        $rawShowColumns = $this->getRawShowColumns($model->getTable());
        $testData       = collect();
        $bar            = $this->createProgressBar($n);
        for ($i = 0; $i < $n; ++$i) {
            $td = [];
            foreach ($columns as $col) {
                if ($rawShowColumns[$col->getName()]->isAutoIncrement()) {
                    // 自動で挿入されるオートインクリメントなカラムは無視
                    continue;
                }
                if (isset($foreignKeys[$col->getName()])) {
                    $td[$col->getName()] = $this->getFkRefValue($col, $foreignKeys[$col->getName()], $casts);
                } else {
                    $td[$col->getName()] = (new MakeTestValue())->getTestValue($col, $model, $rawShowColumns[$col->getName()]);
                }
            }
            $testData->push((new $className())->forceFill($td));
            if ($testData->count() >= 1000 || $i + 1 >= $n) {
                try {
                    $model::bulkInsert($testData);
                } catch (QueryException $e) {
                    if ($e->errorInfo[1] === MySqlErrorCode::ER_DUP_ENTRY) {
                        --$i; // もう一回
                        $this->warn('error occurred, but ignore');
                        $this->warn($e);
                    } else {
                        throw $e;
                    }
                }
                $testData = collect();
            }
            $bar->advance();
        }
        $bar->finish();
        $bar->clear();
    }

    /**
     * @return string[]
     */
    private function getEloquentClassNames(): array
    {
        // プロジェクトの Eloquents を置いてあるディレクトリ以下の PHP ファイルを探索
        // この探索は再帰的に行われる
        // @see https://symfony.com/doc/current/components/finder.html
        $files = (new Finder())->in(app_path('Models/Eloquents'))
            ->files()
            ->name('*.php');
        // 見つかったファイルパスを元にクラス名一覧を作る
        $classNames = [];
        /** @var SplFileInfo $fileInfo */
        foreach ($files->getIterator() as $fileInfo) {
            $classNames[] = str_replace(
            // ファイルパスを名前空間に入れ替え、拡張子を除去
                [app_path('Models/Eloquents'), '/', '.php'],
                ['App\\Models\\Eloquents', '\\', ''],
                $fileInfo->getRealPath()
            );
        }
        // クラス名の中から Eloquent を継承したクラスのみを抜き出す
        $eloquentClassNames = [];
        foreach ($classNames as $className) {
            try {
                // クラス名からインスタンスを作成。 Eloquent を継承しているか確認
                $instance = new $className();
                if ($instance instanceof Model) {
                    $eloquentClassNames[] = $className;
                }
            } /* @noinspection PhpUnusedLocalVariableInspection */ catch (Error $exception) {
                // インスタンス化できない対象について new を行った際のエラーを握りつぶす
                // abstract class や trait が引っかかりやすい
            }
        }

        return $eloquentClassNames;
    }

    /**
     * @throws Exception
     * @return array
     */
    private function getClassNamesForAllOption(): array
    {
        $classNames       = array_unique($this->getEloquentClassNames());
        if ($this->option('ignore')) {
            $ignoreTables = explode(',', $this->option('ignore'));
            $classNames   = array_filter($classNames, static fn ($c) => ! in_array((new $c())->getTable(), $ignoreTables, true));
        }
        $tableNames       = array_map(static fn (string $name) => (new $name())->getTable(), $classNames);
        $sortedTables     = (new DependencySortTable())(
            array_filter(
                $this->getDbal()->listTables(),
                static fn (Table $table) => in_array($table->getName(), $tableNames, true)
            ),
            explode(',', $this->option('ignore'))
        );
        $sortedTableNames = (array_map(static fn (Table $t) => $t->getName(), $sortedTables));
        usort(
            $classNames,
            static fn ($a, $b) => array_search((new $a())->getTable(), $sortedTableNames, true)
                <=> array_search((new $b())->getTable(), $sortedTableNames, true)
        );

        return $classNames;
    }

    /** @var array {[k: テーブル名]: {[p: カラム名]: int[]|string[]}} 外部キー制約の参照先の値のリスト */
    private array $fkRefList = [];

    /**
     * @throws \Exception
     */
    private function getFkRefValue(Column $col, ForeignKeyConstraint $fk, array $casts): int|string|null
    {
        $colName = $col->getName();
        if (! $col->getNotnull() && random_int(1, 100) <= 25) {
            return null;
        }

        $refTableName  = $fk->getForeignTableName();
        $refColumnName = Arr::first($fk->getForeignColumns());

        $this->fkRefList[$refTableName] ??= [];
        $this->fkRefList[$refTableName][$refColumnName] ??= [];
        if (! empty($this->fkRefList[$refTableName][$refColumnName])) {
            return fast_array_random($this->fkRefList[$refTableName][$refColumnName]);
        }
        $this->fkRefList[$refTableName][$refColumnName] = DB::query()
            ->select()
            ->from($refTableName)
            ->get()
            ->map(static fn ($record) => $record->$refColumnName)
            ->toArray();

        $ret = fast_array_random($this->fkRefList[$refTableName][$refColumnName]);

        return match ($casts[$colName]) {
            'string'  => (string) $ret,
            'integer' => (int) $ret,
            'float'   => (float) $ret,
        };
    }

    /**
     * @throws Exception
     * @return AbstractSchemaManager
     */
    private function getDbal(): AbstractSchemaManager
    {
        $dbal = DB::connection()->getDoctrineSchemaManager();
        $dbal->getDatabasePlatform()
            ->registerDoctrineTypeMapping('geometry', 'string');

        return $dbal;
    }

    /**
     * @param  string                  $tableName
     * @return ShowColumnRecordModel[] {[p: カラム名]: インスタンス}
     */
    private function getRawShowColumns(string $tableName): array
    {
        $result = DB::select('show columns from '.$tableName);
        $ret    = [];
        foreach ($result as $col) {
            $ret[$col->Field] = ShowColumnRecordModel::createFromStdObject($col);
        }

        return $ret;
    }
}
