<?php

namespace App\Console\Commands\ForDevelop;

use App\Console\BaseCommand;
use App\Library\DumpSourceCode\Laravel\DumpDeleteController;
use App\Library\DumpSourceCode\Laravel\DumpPresenter;
use App\Library\DumpSourceCode\Laravel\DumpRoute;
use App\Library\DumpSourceCode\Laravel\DumpSearchController;
use App\Library\DumpSourceCode\Laravel\DumpSearchQueryBuilder;
use App\Library\DumpSourceCode\Laravel\DumpShowController;
use App\Library\DumpSourceCode\Laravel\DumpStoreController;
use App\Library\DumpSourceCode\Laravel\DumpStoreRequest;
use App\Library\DumpSourceCode\Laravel\DumpUpdateController;
use App\Library\DumpSourceCode\Laravel\DumpUpdateRequest;
use App\Library\DumpSourceCode\React\DumpReactCreatePageTsx;
use App\Library\DumpSourceCode\React\DumpReactModelFormFieldsScss;
use App\Library\DumpSourceCode\React\DumpReactModelFormFieldsTsx;
use App\Library\DumpSourceCode\React\DumpReactSearchBoxAndTableScss;
use App\Library\DumpSourceCode\React\DumpReactSearchBoxAndTableTsx;
use App\Library\DumpSourceCode\React\DumpReactSearchBoxScss;
use App\Library\DumpSourceCode\React\DumpReactSearchBoxTsx;
use App\Library\DumpSourceCode\React\DumpReactSearchPageScss;
use App\Library\DumpSourceCode\React\DumpReactSearchPageTsx;
use App\Library\DumpSourceCode\React\DumpReactSearchResultTableTsx;
use App\Library\DumpSourceCode\React\DumpReactShowPageTsx;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Input\InputOption;

class DumpController extends BaseCommand
{
    protected $name        = 'dump:controller';
    protected $description = 'コントローラーとそれに伴う色々を自動生成します';

    /**
     * コマンドオプション
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            new InputOption('class', 'c', InputOption::VALUE_REQUIRED, 'コントローラー生成対象クラス'),
            new InputOption('search', 's', InputOption::VALUE_OPTIONAL, '検索のみ'),
        ];
    }

    private string $namespaceDomain = 'Account';
    private string $modelNamespace  = '\\App\\Models\\Eloquents';

    /**
     * @throws Exception
     * @return int
     */
    public function handle(): int
    {
        try {
            $model = new ($this->option('class'))();
        } catch (\Throwable $_) {
            $model = new ($this->modelNamespace.'\\'.$this->option('class'))();
        }

        if (! $this->option('search')) {
            $this->infoStartEnd('DumpPresenter', fn () => (new DumpPresenter($this->namespaceDomain, $model))->dumpToFile());
            $this->infoStartEnd('DumpStoreController', fn () => (new DumpStoreController($this->namespaceDomain, $model))->dumpToFile());
            $this->infoStartEnd('DumpStoreRequest', fn () => (new DumpStoreRequest($this->namespaceDomain, $model))->dumpToFile());
            $this->infoStartEnd('DumpShowController', fn () => (new DumpShowController($this->namespaceDomain, $model))->dumpToFile());
        }
        $this->infoStartEnd('DumpSearchController', fn () => (new DumpSearchController($this->namespaceDomain, $model))->dumpToFile());
        $this->infoStartEnd('DumpSearchQueryBuilder', fn () => (new DumpSearchQueryBuilder($this->namespaceDomain, $model))->dumpToFile());
        if (! $this->option('search')) {
            $this->infoStartEnd('DumpUpdateController', fn () => (new DumpUpdateController($this->namespaceDomain, $model))->dumpToFile());
            $this->infoStartEnd('DumpUpdateRequest', fn () => (new DumpUpdateRequest($this->namespaceDomain, $model))->dumpToFile());
            $this->infoStartEnd('DumpDeleteController', fn () => (new DumpDeleteController($this->namespaceDomain, $model))->dumpToFile());
            $this->infoStartEnd('DumpRoute', fn () => (new DumpRoute($this->namespaceDomain, $model))->dumpToFile());
            $this->infoStartEnd('DumpReactCreatePageTsx', fn () => (new DumpReactCreatePageTsx($this->namespaceDomain, $model))->dumpToFile());
            $this->infoStartEnd('DumpReactModelFormFieldsScss', fn () => (new DumpReactModelFormFieldsScss($this->namespaceDomain, $model))->dumpToFile());
            $this->infoStartEnd('DumpReactModelFormFieldsTsx', fn () => (new DumpReactModelFormFieldsTsx($this->namespaceDomain, $model))->dumpToFile());
        }
        $this->infoStartEnd('DumpReactSearchBoxScss', fn () => (new DumpReactSearchBoxScss($this->namespaceDomain, $model))->dumpToFile());
        $this->infoStartEnd('DumpReactSearchBoxTsx', fn () => (new DumpReactSearchBoxTsx($this->namespaceDomain, $model))->dumpToFile());
        $this->infoStartEnd('DumpReactSearchPageScss', fn () => (new DumpReactSearchPageScss($this->namespaceDomain, $model))->dumpToFile());
        $this->infoStartEnd('DumpReactSearchPageTsx', fn () => (new DumpReactSearchPageTsx($this->namespaceDomain, $model))->dumpToFile());
        $this->infoStartEnd('DumpReactSearchResultTableTsx', fn () => (new DumpReactSearchResultTableTsx($this->namespaceDomain, $model))->dumpToFile());
        $this->infoStartEnd('DumpReactSearchBoxAndTableTsx', fn () => (new DumpReactSearchBoxAndTableTsx($this->namespaceDomain, $model))->dumpToFile());
        $this->infoStartEnd('DumpReactSearchBoxAndTableScss', fn () => (new DumpReactSearchBoxAndTableScss($this->namespaceDomain, $model))->dumpToFile());
        if (! $this->option('search')) {
            $this->infoStartEnd('DumpReactShowPageTsx', fn () => (new DumpReactShowPageTsx($this->namespaceDomain, $model))->dumpToFile());
        }

        return static::SUCCESS;
    }
}
