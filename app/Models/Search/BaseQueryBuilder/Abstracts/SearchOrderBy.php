<?php

namespace App\Models\Search\BaseQueryBuilder\Abstracts;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;

class SearchOrderBy implements SearchableOrderByContract
{
    /**
     * @var Closure
     */
    public $orderByClosure;

    /**
     * OrderBy constructor.
     * @param Closure|Builder|string $orderBy
     */
    public function __construct($orderBy)
    {
        if (is_string($orderBy) || $orderBy instanceof Expression) {
            $this->orderByClosure = static function ($query, $direction) use ($orderBy) {
                /* @var Builder $query */
                return $query->orderBy($orderBy, $direction);
            };
        } else {
            $this->orderByClosure = $orderBy;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildQuery($query, $direction = 'asc')
    {
        return $this->orderByClosure->__invoke($query, $direction);
    }
}
