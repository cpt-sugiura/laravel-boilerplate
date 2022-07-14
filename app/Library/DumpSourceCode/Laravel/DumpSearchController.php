<?php

namespace App\Library\DumpSourceCode\Laravel;

use App\Library\DumpSourceCode\DumpSourceCode;

class DumpSearchController extends DumpSourceCode
{
    protected function viewFilePath(): string
    {
        return $this->codeStubDirRootPath().'/SearchController.php.stub.blade.php';
    }

    protected function distFilePath(): string
    {
        $p = $this->viewParams();

        return app_path('Http/Controllers/'.$p['domain'].'BrowserAPI/'.$p['classBaseName'].'/'.$p['classBaseName'].'SearchController.php');
    }
}
