<?php

namespace Database\Seeders;

use App\Models\Eloquents\BaseEloquent;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class BaseSeeder extends Seeder
{
    /**
     * BaseSeeder constructor.
     */
    public function __construct()
    {
        ini_set('memory_limit', '1G');
    }

    /**
     * シーダーとして走るコード
     */
    abstract public function run(): void;

    /**
     * 色々情報の詰まったフォーマットに改造したプログレスバーを生成して返す。
     * @param  int         $max
     * @return ProgressBar
     */
    protected function createProgressBar($max = 0): ProgressBar
    {
        $progressBar = new ProgressBar(new ConsoleOutput(), $max);
        $progressBar->setFormat('%current%/%max% [%bar%] %percent%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->setEmptyBarCharacter(' ');
        $progressBar->setBarCharacter('=');
        $progressBar->setProgressCharacter('>');

        return $progressBar;
    }

    protected function clearLine(): void
    {
        echo "\033[2K\r";
    }

    /**
     * どのモデルでも共通の値をインスタンスにセットする
     * @return callable
     */
    protected function getClosureOfSetCommonValueToModelInstance(): callable
    {
        return static function (BaseEloquent $eloquent): BaseEloquent {
            $eloquent->created_at ??= $now = date('Y-m-d H:i:s');
            $eloquent->updated_at ??= $now;
            $eloquent->setHidden([]);

            return $eloquent;
        };
    }
}
