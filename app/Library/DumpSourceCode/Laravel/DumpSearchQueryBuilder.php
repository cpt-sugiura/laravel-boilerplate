<?php

namespace App\Library\DumpSourceCode\Laravel;

use App\Library\DumpSourceCode\DumpSourceCode;
use Doctrine\DBAL\Exception;

class DumpSearchQueryBuilder extends DumpSourceCode
{
    protected function viewFilePath(): string
    {
        return $this->codeStubDirRootPath().'/ModelSearchQueryBuilder.php.stub.blade.php';
    }

    /**
     * @throws Exception
     * @return string
     */
    protected function distFilePath(): string
    {
        $p = $this->viewParams();

        return app_path('Models/Search/'.$p['domain'].'API/'.$p['classBaseName'].'/'.$p['classBaseName'].'SearchQueryBuilder.php');
    }
}
