<?php

namespace App\Library\DumpSourceCode\Laravel;

use App\Library\DumpSourceCode\DumpSourceCode;

class DumpUpdateRequest extends DumpSourceCode
{
    protected function viewFilePath(): string
    {
        return $this->codeStubDirRootPath().'/UpdateRequest.php.stub.blade.php';
    }

    protected function distFilePath(): string
    {
        $p = $this->viewParams();

        return app_path('Http/Requests/'.$p['domain'].'API/'.$p['classBaseName'].'/'.$p['classBaseName'].'UpdateRequest.php');
    }
}
