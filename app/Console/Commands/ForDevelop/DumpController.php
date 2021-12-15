<?php

namespace App\Console\Commands\ForDevelop;

use App\Console\BaseCommand;

class DumpController extends BaseCommand
{
    protected $name        = 'dump:controller';
    protected $description = 'コントローラを自動生成します';

    public function handle(): int
    {
        $controller = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'CodeStab'.DIRECTORY_SEPARATOR.'ResourceController.php.stub.blade.php');

        $phpCode = \Blade::compileString($controller);
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'tmp.php', $phpCode);

        return static::SUCCESS;
    }
}
