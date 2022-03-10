<?php

namespace App\Console\Commands\ForDevelop;

use DB;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Str;
use Symfony\Component\Console\Input\InputOption;

class DumpModelFromDB extends Command
{
    /**
     * コマンド名
     *
     * @var string
     */
    protected $name = 'dump:model-from-db';

    /**
     * コマンド説明
     *
     * @var string
     */
    protected $description = 'データベース中のテーブルを元にEloquentモデルを生成';

    /**
     * @var Table[] モデル生成元テーブルインスタンス配列
     */
    private array $tgtTables;

    /**
     * コマンドオプション
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'テーブルに対応する全モデルを生成'],
            ['table', 't', InputOption::VALUE_OPTIONAL, 'モデル生成元のテーブルを指定'],
        ];
    }

    /**
     * 実行内容
     *
     * @throws Exception
     * @return void
     */
    public function handle(): void
    {
        $schemaManager = DB::getDoctrineSchemaManager();
        $schemaManager->getDatabasePlatform()
            ->registerDoctrineTypeMapping('geometry', 'string');
        if ($this->option('table')) {
            $this->tgtTables = [$schemaManager->listTableDetails($this->option('table'))];
        } elseif ($this->option('all')) {
            $this->tgtTables = $schemaManager->listTables();
        } else {
            $this->error('--all オプションを付けるか、-t [テーブル名] でテーブル名を指定する必要があります');
        }

        $this->createCommands()->each(
            function (string $command) {
                $this->info($command);
                exec($command);
            }
        );
    }

    /**
     * 実行すべきコマンド達を格納した一次元Collectionを返す
     * @return Collection
     */
    private function createCommands(): Collection
    {
        $phpCsFixerPath = implode(DIRECTORY_SEPARATOR, ['vendor', 'bin', 'php-cs-fixer']);

        return collect($this->tgtTables ?? [])
            ->map(
                static function (Table $table) use ($phpCsFixerPath) {
                    $tableName = $table->getName();
                    if ($tableName === 'migrations') {
                        return [];
                    }
                    $modelName          = ucfirst(Str::camel(Str::singular($table->getName())));
                    $modelFilePath      = config('infyom.laravel_generator.path.model').$modelName.'.php';
                    $modelNamespacePath = str_replace('\\', '\\\\', config('infyom.laravel_generator.namespace.model').'\\'.$modelName);

                    return [
                        "php artisan dev:dump-simple-model-by-dbal --table=${tableName}",
                        "php artisan dev:replace-model-rules ${modelName}",
                        "php artisan dev:append-model-rule-attributes ${modelNamespacePath}",
                        "php artisan dev:append-model-soft-delete ${modelNamespacePath}",
                        "php artisan ide-helper:model -Wr ${modelNamespacePath}",
                        "${phpCsFixerPath} fix -vvv --config .php-cs-fixer.php ${modelFilePath}",
                    ];
                }
            )
            ->flatten();
    }
}
