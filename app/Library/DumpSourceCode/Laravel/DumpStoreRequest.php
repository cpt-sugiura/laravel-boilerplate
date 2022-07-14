<?php

namespace App\Library\DumpSourceCode\Laravel;

use App\Library\DumpSourceCode\DumpSourceCode;

class DumpStoreRequest extends DumpSourceCode
{
    protected function viewFilePath(): string
    {
        return $this->codeStubDirRootPath().'/StoreRequest.php.stub.blade.php';
    }

    protected function distFilePath(): string
    {
        $p = $this->viewParams();

        return app_path('Http/Requests/'.$p['domain'].'API/'.$p['classBaseName'].'/'.$p['classBaseName'].'StoreRequest.php');
    }
}
