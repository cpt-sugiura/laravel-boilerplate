<?php

namespace App\Library\DumpSourceCode\Laravel;

use App\Library\DumpSourceCode\DumpSourceCode;

class DumpUpdateController extends DumpSourceCode
{
    protected function viewFilePath(): string
    {
        return $this->codeStubDirRootPath().'/UpdateController.php.stub.blade.php';
    }

    protected function distFilePath(): string
    {
        $p = $this->viewParams();

        return app_path('Http/Controllers/'.$p['domain'].'BrowserAPI/'.$p['classBaseName'].'/'.$p['classBaseName'].'UpdateController.php');
    }
}
