<?php

namespace App\Console\Commands\ForDevelop;

use App\Console\BaseCommand;
use App\Models\Eloquents\BaseEloquent;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
            new InputOption('table', 't', InputOption::VALUE_OPTIONAL, 'モデル生成元のテーブルを指定。namespace含むクラス名'),
            new InputOption('count', 'c', InputOption::VALUE_OPTIONAL, '生成する1テーブル毎のテストデータの個数', 100)
        ];
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     * @throws \Exception
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('table')) {
            $classNames = [$this->option('table')];
        } elseif ($this->option('all')) {
            $classNames = self::getEloquentClassNames();
        } else {
            $this->error('--all オプションを付けるか、-t [テーブル名] でテーブル名を指定する必要があります');

            return static::FAILURE;
        }

        $n = $this->option('count');
        foreach ($classNames as $className) {
            $this->info($className);
            $this->main($className, $n);
        }

        return static::SUCCESS;
    }

    private function makeStringTestData(Column $col): string
    {
        $base    = ($col->getComment() ?? $col->getName()).'_'.Str::random(4);
        $trimmed = substr($base, -$col->getLength());

        return iconv('UTF-8', 'UTF-8//IGNORE', $trimmed);
    }

    /**
     * @param  Column          $col
     * @param  array           $casts
     * @throws \Exception
     * @return string|int|null
     */
    protected function getTestValue(Column $col, array $casts): int|string|null
    {
        $colName = $col->getName();
        if (! $col->getNotnull() && random_int(1, 100) <= 25) {
            $val = null;
        } else {
            $val = match ($casts[$colName]) {
                'string' => $this->makeStringTestData($col),
                'date'   => date('Y-m-d H:i:s', random_int(strtotime('-1 months'), strtotime('+1 months'))),
                'integer', 'float' => random_int(0, 10000),
            };
        }

        return $val;
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
        $model    = new $className();
        $columns  = $model->getConnection()->getDoctrineSchemaManager()
            ->listTableColumns($model->getTable());
        $casts    = $model->getCasts();
        $testData = collect();
        $bar      = $this->createProgressBar($n);
        for ($i = 0; $i < $n; ++$i) {
            $td = [];
            foreach ($columns as $col) {
                $td[$col->getName()] = $this->getTestValue($col, $casts);
            }
            $testData->push((new $className())->forceFill($td));
            if ($testData->count() >= 1000) {
                $model::bulkInsert($testData);
                $testData = collect();
            }
            $bar->advance();
        }
        $model::bulkInsert($testData);
        $bar->finish();
        $bar->clear();
    }

    /**
     * @return array
     */
    protected static function getEloquentClassNames(): array
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
                if ($instance instanceof \Illuminate\Database\Eloquent\Model) {
                    $eloquentClassNames[] = $className;
                }
            } catch (\Error $exception) {
                // インスタンス化できない対象について new を行った際のエラーを握りつぶす
                // abstract class や trait が引っかかりやすい
            }
        }

        return $eloquentClassNames;
    }
}
