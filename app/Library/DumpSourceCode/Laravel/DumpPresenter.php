<?php

namespace App\Library\DumpSourceCode\Laravel;

use App\Library\DumpSourceCode\DumpSourceCode;

class DumpPresenter extends DumpSourceCode
{
    protected function viewFilePath(): string
    {
        return $this->codeStubDirRootPath().'/ModelPresenter.php.stub.blade.php';
    }

    protected function distFilePath(): string
    {
        $p = $this->viewParams();

        return app_path('Http/Controllers/'.$p['domain'].'BrowserAPI/Presenters/'.$p['classBaseName'].'Presenter.php');
    }
}
