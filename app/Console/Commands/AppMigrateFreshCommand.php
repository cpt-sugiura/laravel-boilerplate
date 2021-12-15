<?php

namespace App\Console\Commands;

use Illuminate\Database\Console\Migrations\FreshCommand;

class AppMigrateFreshCommand extends FreshCommand
{
    protected $description = '全ての table と view を Drop して全ての migrations を再度動かす';

    public function handle(): int
    {
        $this->input->setOption('drop-views', true);

        return parent::handle();
    }
}
