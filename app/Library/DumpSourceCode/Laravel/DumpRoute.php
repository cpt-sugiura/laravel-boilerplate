<?php

namespace App\Library\DumpSourceCode\Laravel;

use App\Library\DumpSourceCode\DumpSourceCode;
use Str;

class DumpRoute extends DumpSourceCode
{
    protected function viewFilePath(): string
    {
        return $this->codeStubDirRootPath().'/routing.php.stub.blade.php';
    }

    protected function distFilePath(): string
    {
        $p = $this->viewParams();

        return base_path('/routes/'.Str::snake($p['domain']).'_browser_api/'.Str::snake($p['classBaseName']).'.php');
    }
}
