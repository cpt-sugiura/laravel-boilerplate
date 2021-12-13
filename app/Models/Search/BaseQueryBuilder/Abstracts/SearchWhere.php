<?php

namespace App\Models\Search\BaseQueryBuilder\Abstracts;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;

/**
 * 検索用WHERE句ビルダ。
 *
 * Class SearchQueryClosure
 * @package App\Models\Search\BaseQueryBuilder\Abstracts
 */
class SearchWhere implements SearchableWhereContract
{
    /**
     * @var Closure
     */
    private Closure $isSkip;
    /**
     * @var Closure
     */
    private $whereClosure;

    /**
     * SearchQueryClosure constructor.
     * @param Closure|string|Expression|array $where  検索内容。第一引数がBuilder, 第二引数が検索で渡された条件のクロージャを想定。 stringかExpressionの場合は WHERE $where = $valueの動作になる。arrayの場合は WHERE $where[0] $where[1] $value の動作になる
     * @param Closure|null                    $isSkip ($value: mixed) => bool 検索用のWHERE句をスキップするためのクロージャ。nullが渡された場合とデフォルトの場合では, 検索条件パラメータが null か空文字列か空配列の場合にスキップの動作になる。
     */
    public function __construct($where, ?Closure $isSkip = null)
    {
        if (is_string($where) || $where instanceof Expression) {
            $this->whereClosure = static function ($query, $value) use ($where) {
                if (! is_string($value) && ! is_numeric($value)) {
                    return $query;
                }
                /* @var Builder $query */
                return $query->where($where, '=', $value);
            };
        } elseif (is_array($where)) {
            $this->whereClosure = static function ($query, $value) use ($where) {
                if (! is_string($value) && ! is_numeric($value)) {
                    return $query;
                }
                /* @var Builder $query */
                return $query->where($where[0], $where[1] ?? '=', $value);
            };
        } else {
            $this->whereClosure = $where;
        }
        $this->isSkip = $isSkip ?? static function ($value) {
            return $value === null || $value === '' || $value === [];
        };
    }

    /**
     * {@inheritdoc}
     */
    public function buildQuery($query, $value)
    {
        if (! $this->isSkip->__invoke($value)) {
            return $this->whereClosure->__invoke($query, $value);
        }

        return $query;
    }
}
