<?php

namespace App\Library\DumpSourceCode;

use App\Models\Eloquents\BaseEloquent;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Str;
use View;

class DumpSourceCodeGetSearchMethodsForReact
{
    public function __construct(
        private readonly BaseEloquent $model,
        private readonly AbstractSchemaManager $schemeManager
    ) {
    }

    /**
     * @param  ColumnForRender[]      $searchableColumns
     * @param  ForeignKeyConstraint[] $foreignKeys
     * @throws Exception
     * @return array
     */
    public function __invoke(array $searchableColumns, array $foreignKeys): array
    {
        $searchMethods = [];
        foreach ($searchableColumns as $col) {
            if ($col->column->getName() === $this->model->getKeyName()) {
                continue;
            }
            $castType = $col->getCastType();
            if (in_array($castType, ['integer', 'numeric'], true)) {
                $searchMethods = [...$searchMethods, ...$this->getNumber($col, $foreignKeys)];
            } elseif (in_array($castType, ['date', 'datetime'], true)) {
                $searchMethods[] = $this->getDateRange($col);
            } elseif ($castType === 'boolean') {
                $searchMethods[] = $this->getBoolean($col);
            } else {
                // string 型など
                $searchMethods[] = [
                    'label' => $col->getLabel(),
                    'name'  => Str::camel($col->column->getName()),
                ];
            }
        }

        return $searchMethods;
    }

    /**
     * @param  ColumnForRender $col
     * @return array
     */
    protected function getBoolean(ColumnForRender $col): array
    {
        $base = [
            'type'  => 'Boolean',
            'name'  => Str::camel($col->column->getName()),
            'label' => $col->getLabel(),
        ];

        return [
            ...$base,
            'rendered' => View::file(
                __DIR__.'/CodeStab/React/component/BooleanInput.tsx.stub.blade.php',
                $base
            )->render(),
        ];
    }

    /**
     * @param  ColumnForRender $col
     * @param  array           $foreignKeys
     * @throws Exception
     * @return array
     */
    protected function getNumber(ColumnForRender $col, array $foreignKeys): array
    {
        if (! str_ends_with($col->column->getName(), 'id')) {
            return [
                [
                    'label' => $col->getLabel(),
                    'name'  => Str::camel($col->column->getName().'_start'),
                ],
                [
                    'label' => '',
                    'name'  => Str::camel($col->column->getName().'_end'),
                ]
            ];
        }

        /** @var ForeignKeyConstraint|null $currentFk */
        $currentFk = null;
        foreach ($foreignKeys as $fk) {
            if ($fk->getLocalColumns()[0] === $col->column->getName()) {
                $currentFk = $fk;
                break;
            }
        }

        $ret = [];
        if (isset($currentFk)) {
            $relTableColumns = $this->schemeManager->listTableColumns($currentFk->getForeignTableName());
            foreach ($relTableColumns as $column) {
                if (str_ends_with($column->getName(), '_name')) {
                    $ret[] = [
                        'label' => $col->getLabel(),
                        'name'  => Str::camel(str_replace('_id', '_name', $col->column->getName())),
                    ];
                }
            }
        } else {
            $ret[] = [
                'label' => '',
                'name'  => Str::camel(str_replace('_id', '_name', $col->column->getName())),
            ];
        }

        return $ret;
    }

    /**
     * @param  ColumnForRender $col
     * @return array
     */
    protected function getDateRange(ColumnForRender $col): array
    {
        $base = [
            'type'      => 'DateRange',
            'name'      => Str::camel($col->column->getName()),
            'label'     => $col->getLabel(),
            'startName' => Str::camel($col->column->getName().'_start'),
            'endName'   => Str::camel($col->column->getName().'_end'),
            'gridSpace' => 2,
        ];

        return [
            ...$base,
            'rendered' => View::file(
                __DIR__.'/CodeStab/React/component/DateRangeInput.tsx.stub.blade.php',
                $base
            )->render(),
        ];
    }
}
