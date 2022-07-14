<?php

namespace App\Library\DumpSourceCode\React;

use App\Library\DumpSourceCode\DumpSourceCode;

class DumpReactSearchPageTsx extends DumpSourceCode
{
    protected function viewFilePath(): string
    {
        return __DIR__.'/../CodeStab/React/page/ModelSearchPage.tsx.stub.blade.php';
    }

    protected function distFilePath(): string
    {
        $p = $this->viewParams();

        return resource_path('/js/'.\Str::camel($p['domain']).'/pages/'.\Str::camel($p['classBaseName']).'/'.$p['classBaseName'].'SearchPage.tsx');
    }
}
