<?php

namespace App\Models\Search\BaseQueryBuilder\Abstracts;

use Illuminate\Database\Query\Builder;

/**
 * 検索条件として構築されるWhere句表現クラスの守るべき契約
 * Interface SearchableWhereContract
 * @package App\Models\Search\BaseQueryBuilder\Abstracts
 */
interface SearchableWhereContract
{
    /**
     * クエリを構築して構築後のクエリを返す
     * @param  Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param  mixed                                         $value
     * @return \Illuminate\Database\Eloquent\Builder|Builder
     */
    public function buildQuery($query, $value);
}
