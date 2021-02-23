<?php

namespace App\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * コマンド基底クラス.
 * Commandsディレクトリ以下に入れるとArtisan Commandの一つとしてloadされるのでConsole直下に配置
 * Class BaseCommand
 * @package App\Console
 */
abstract class BaseCommand extends Command
{
    /**
     * 処理本体
     */
    abstract public function handle(): void;

    /**
     * Windowsでも見やすいエラー表示
     * @param string $string
     * @param null   $verbosity
     */
    public function error($string, $verbosity = null): void
    {
        $this->output->writeln("<fg=red;options=bold,underscore>${string}</>", $this->parseVerbosity($verbosity));
    }

    /**
     * コンソール上での文字列強調
     * @param  string|int|float $str
     * @return string
     */
    protected static function highlight($str): string
    {
        return '<fg=white;options=bold,underscore>'.$str.'</>';
    }

    /**
     * 色々情報の詰まったフォーマットに改造したプログレスバーを生成して返す。
     * @param  int         $max
     * @return ProgressBar
     */
    protected function createProgressBar($max = 0): ProgressBar
    {
        $progressBar = $this->output->createProgressBar($max);
        $progressBar->setFormat('%current%/%max% [%bar%] %percent%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->setEmptyBarCharacter(' ');
        $progressBar->setBarCharacter('=');
        $progressBar->setProgressCharacter('>');

        return $progressBar;
    }
}
