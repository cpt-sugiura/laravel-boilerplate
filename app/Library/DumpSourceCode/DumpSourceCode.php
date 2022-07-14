<?php

namespace App\Library\DumpSourceCode;

use App\Models\Eloquents\BaseEloquent;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Illuminate\Database\Eloquent\SoftDeletes;
use RuntimeException;
use Str;
use View;

abstract class DumpSourceCode
{
    /**
     * @param string       $domain ファイルパス、ネームスペース。小文字大文字問わず
     * @param BaseEloquent $model
     */
    public function __construct(
        protected string $domain,
        protected BaseEloquent $model,
    ) {
    }

    /**
     * @throws Exception
     * @return array{domain: string, classBaseName: string, classFullName: string, classNaturalName: string, primaryKey: string, tableName: string, hasDeletedAt: boolean, visibleColumnNames: array, visibleColumns: array, searchableColumnNames: array, searchableColumns: array, editableColumnNames: array, editableColumns: array, searchMethods: array, searchMethodsForReact: array}
     */
    protected function viewParams(): array
    {
        $domain        = $this->domain;
        $classBaseName = class_basename($this->model);
        /** @var BaseEloquent $classFullName */
        $classFullName    = get_class($this->model);
        $classNaturalName = $classFullName::getNaturalLanguageName();
        $primaryKey       = $this->model->getKeyName();
        $tableName        = $this->model->getTable();
        $hasDeletedAt     = in_array(SoftDeletes::class, class_uses($this->model), true);

        $columnsForRender = [];
        foreach ($this->getDoctrineSchemaManager()->listTableColumns($tableName) as $c) {
            $cr = new ColumnForRender($this->model, $c);
            if (count($cr->foreignKeys()) > 0) {
                $columnsForRender = [
                    ...$columnsForRender,
                    ...$cr->foreignKeyColumns(),
                ];
            } else {
                $columnsForRender[] = $cr;
            }
        }

        /** @var ColumnForRender[] $searchableColumns */
        $searchableColumns = array_filter($columnsForRender, static fn (ColumnForRender $c) => $c->searchable());
        $searchJoinList    = [];
        foreach ($searchableColumns as $c) {
            if ($c->model->getTable() !== $tableName) {
                $searchJoinList[] = [
                    'join',
                    $c->model->getTable(),
                    $c->model->getTable().'.'.$c->model->getKeyName(),
                    '=',
                    $tableName.'.'.$c->model->getKeyName()
                ];
            }
        }
        $foreignKeys           = $this->getDoctrineSchemaManager()->listTableForeignKeys($tableName);
        $searchMethods         = $this->getSearchMethodsForPHP($searchableColumns, $foreignKeys);
        $searchMethodsForReact = (new DumpSourceCodeGetSearchMethodsForReact($this->model, $this->getDoctrineSchemaManager()))($searchableColumns, $foreignKeys);

        return compact(
            'domain', 'classBaseName', 'classFullName', 'classNaturalName', 'primaryKey',
            'tableName', 'hasDeletedAt', 'columnsForRender', 'searchJoinList',
            'searchMethods', 'searchMethodsForReact',
        );
    }

    /**
     * @param  string                          $filePath
     * @param  \Illuminate\Contracts\View\View $view
     * @return void
     */
    protected function filePutContents(string $filePath, \Illuminate\Contracts\View\View $view): void
    {
        $dir = dirname($filePath);
        if (! is_dir($dir) && ! mkdir($dir) && ! is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        file_put_contents($filePath, str_replace('%%php%%', '<?php', $view->render()));
    }

    protected function codeStubDirRootPath(): string
    {
        return __DIR__.'/CodeStab';
    }

    abstract protected function viewFilePath(): string;

    abstract protected function distFilePath(): string;

    /**
     * @throws Exception
     * @return void
     */
    public function dumpToFile(): void
    {
        $view     = View::file($this->viewFilePath(), $this->viewParams());
        $filePath = $this->distFilePath();
        $this->filePutContents($filePath, $view);
    }

    /**
     * @param  ColumnForRender[]      $searchableColumns
     * @param  ForeignKeyConstraint[] $foreignKeys
     * @throws Exception
     * @return array
     */
    protected function getSearchMethodsForPHP(array $searchableColumns, array $foreignKeys): array
    {
        $searchMethods = [];
        foreach ($searchableColumns as $col) {
            $castType = $col->getCastType();
            if ($castType === 'string') {
                $searchMethods[] = [
                    'label'  => $col->getLabel(),
                    'name'   => $col->getNameAsRequestKey(),
                    'method' => "SearchWhereMacro::partialMatch('".$col->getFullNameInDB()."')"
                ];
            } elseif (in_array($castType, ['integer', 'numeric'], true)) {
                if (! preg_match('#id$#', $col->column->getName())) {
                    $searchMethods[] = [
                        'label'  => $col->getLabel(),
                        'name'   => Str::camel($col->column->getName().'_start'),
                        'method' => "['".$col->getFullNameInDB()."','>=']"
                    ];
                    $searchMethods[] = [
                        'label'  => '',
                        'name'   => Str::camel($col->column->getName().'_end'),
                        'method' => "['".$col->getFullNameInDB()."','<=']"
                    ];
                } else {
                    /** @var ForeignKeyConstraint|null $currentFk */
                    $currentFk = null;
                    foreach ($foreignKeys as $fk) {
                        if ($fk->getLocalColumns()[0] === $col->column->getName()) {
                            $currentFk = $fk;
                            break;
                        }
                    }
                    if (isset($currentFk)) {
                        $relTableColumns = $this->getDoctrineSchemaManager()->listTableColumns($currentFk->getForeignTableName());
                        foreach ($relTableColumns as $fkCol) {
                            if (preg_match('/_name$/', $fkCol->getName())) {
                                $searchMethods[] = [
                                    'label'  => explode('。', $fkCol->getComment())[0],
                                    'name'   => Str::camel($fkCol->getName()),
                                    'method' => "SearchWhereMacro::partialMatch('".$currentFk->getForeignTableName().'.'.$fkCol->getName()."')"
                                ];
                            }
                        }
                    } else {
                        $searchMethods[] = [
                            'label'  => '',
                            'name'   => Str::camel($col->column->getName()),
                            'method' => "'".$col->getFullNameInDB()."'"
                        ];
                    }
                }
            } elseif (in_array($castType, ['date', 'datetime'], true)) {
                $searchMethods[] = [
                    'label'  => $col->getLabel().'(開始)',
                    'name'   => Str::camel($col->column->getName().'_start'),
                    'method' => "SearchWhereMacro::date('".$col->getFullNameInDB()."','>=')"
                ];
                $searchMethods[] = [
                    'label'  => $col->getLabel().'(終了)',
                    'name'   => Str::camel($col->column->getName().'_end'),
                    'method' => "SearchWhereMacro::date('".$col->getFullNameInDB()."','<=')"
                ];
            } else {
                $searchMethods[] = [
                    'label'  => $col->getLabel(),
                    'name'   => Str::camel($col->column->getName()),
                    'method' => "'".$col->getFullNameInDB()."'"
                ];
            }
        }

        return $searchMethods;
    }

    /**
     * @return AbstractSchemaManager
     */
    protected function getDoctrineSchemaManager(): AbstractSchemaManager
    {
        return $this->model->getConnection()->getDoctrineSchemaManager();
    }
}
