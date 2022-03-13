<?php

namespace App\Models\Search\AdminAPI\Admin;

use App\Models\Eloquents\Admin\Admin;
use App\Models\Search\BaseQueryBuilder\BaseSearchQueryBuilder;
use App\Models\Search\BaseQueryBuilder\Macros\SearchWhereMacro;
use DB;
use Illuminate\Database\Query\Builder;

class AdminSearchQueryBuilder extends BaseSearchQueryBuilder
{
    public function select(): array
    {
        return [
            'admins.admin_id',
            'admins.name',
            'admins.email',
            'admins.created_at',
            'admins.updated_at',
        ];
    }

    /**
     * @return Builder
     */
    protected function from(): Builder
    {
        return DB::query()->from((new Admin())->getTable())
            ->whereNull('admins.deleted_at');
    }

    protected function searchableWhereFields(): array
    {
        return [
          'name'  => SearchWhereMacro::partialMatch('admins.name'),
          'email' => SearchWhereMacro::partialMatch('admins.email'),
        ];
    }

    protected function orderByAbleFields(): array
    {
        return [
            'name'       => 'admins.name',
            'email'      => 'admins.email',
            'createdAt'  => 'admins.created_at',
            'updatedAt'  => 'admins.updated_at',
        ];
    }
}
