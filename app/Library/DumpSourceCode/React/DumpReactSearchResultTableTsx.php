<?php

namespace App\Library\DumpSourceCode\React;

use App\Library\DumpSourceCode\DumpSourceCode;

class DumpReactSearchResultTableTsx extends DumpSourceCode
{
    protected function viewFilePath(): string
    {
        return __DIR__.'/../CodeStab/React/component/ModelSearchResultTable.tsx.stub.blade.php';
    }

    protected function distFilePath(): string
    {
        $p = $this->viewParams();

        return resource_path('/js/'.\Str::camel($p['domain']).'/component/'.\Str::camel($p['classBaseName']).'/'.$p['classBaseName'].'SearchResultTable.tsx');
    }
}