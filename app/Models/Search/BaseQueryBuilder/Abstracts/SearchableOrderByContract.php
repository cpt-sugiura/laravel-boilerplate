<?php

namespace App\Models\Search\BaseQueryBuilder\Abstracts;

use Illuminate\Database\Query\Builder;

/**
 * 検索条件として構築されるORDER BY句表現クラスの守るべき契約
 * Interface SearchableOrderByContract
 * @package App\Models\Search\BaseQueryBuilder\Abstracts
 */
interface SearchableOrderByContract
{
    /**
     * クエリを構築して構築後のクエリを返す
     * @param Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                         $direction
     * @returnd \Illuminate\Database\Eloquent\Builder|Builder
     */
    public function buildQuery($query, $direction);
}
