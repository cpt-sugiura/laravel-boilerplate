<?php

namespace App\Models\Search\BaseQueryBuilder\Macros;

use Closure;
use DateTime;
use DB;
use Exception;
use Illuminate\Database\Query\Builder;

/**
 * 検索時に使いまわしそうな、汎用的なWhere句クロージャを記述するクラス
 * Class WhereMacro
 * @package App\Models\Search\BaseQueryBuilder\Macros
 */
class SearchWhereMacro
{
    /**
     * WHERE ${column} in (${values})
     * @param  string  $column
     * @return Closure
     */
    public static function in(string $column): callable
    {
        return static function ($query, $values) use ($column) {
            if (is_string($values) || is_numeric($values)) {
                $values = [$values];
            }
            /* @var Builder $query */
            return $query->whereIn($column, $values);
        };
    }

    /**
     * WHERE ${column} is null
     * @param  string  $column
     * @return Closure
     */
    public static function isNull(string $column): callable
    {
        return static function ($query, $value) use ($column) {
            /* @var Builder $query */
            return $value ? $query->whereNull($column) : $query;
        };
    }

    /**
     * WHERE ${column} is not null
     * @param  string  $column
     * @return Closure
     */
    public static function isNotNull(string $column): callable
    {
        return static function ($query, $value) use ($column) {
            /* @var Builder $query */
            return $value ? $query->whereNotNull($column) : $query;
        };
    }

    /**
     * WHERE (${column} = ${value} or ${column} is null)
     * @param  string  $column
     * @return Closure
     */
    public static function orNull(string $column): callable
    {
        return static function ($query, $value) use ($column) {
            return $query->where(
                static function ($query) use ($value, $column) {
                    /* @var Builder $query */
                    return $query->orWhere($column, $value)->orWhereNull($column);
                }
            );
        };
    }

    /**
     * WHERE (${columns[0]} = ${value} or ${column[1]} = ${value} ...)
     * @param  array   $columns
     * @return Closure
     */
    public static function columnOr(array $columns): callable
    {
        return static function ($query, $value) use ($columns) {
            return $query->where(static function ($query) use ($value, $columns) {
                foreach ($columns as $c) {
                    /** @var Builder $query */
                    $query = $query->orWhere($c, $value);
                }

                return $query;
            });
        };
    }

    /**
     * WHERE date(${column}) ${operator} ${value}
     * @param  string  $column
     * @param  string  $operator
     * @return Closure
     */
    public static function date(string $column, string $operator = '='): callable
    {
        return static function ($query, $value) use ($operator, $column) {
            try {
                $dateTimeValue = new DateTime($value);
            } catch (Exception $e) {
                // 日付生成エラーを握りつぶし日付検索条件を無視して検索続行
                return $query;
            }
            /* @var Builder $query */
            return $query->whereDate($column, $operator, $dateTimeValue->format('Y-m-d'));
        };
    }

    /**
     * WHERE DATE_FORMAT(${column}, '%m%d') ${operator} DATE_FORMAT(${value}, '%m%d')
     * @param  string  $column
     * @param  string  $operator
     * @return Closure
     */
    public static function dateAsCycle(string $column, string $operator = '='): callable
    {
        return static function ($query, $value) use ($operator, $column) {
            try {
                $dateFormatted = (new DateTime($value))->format('Y-m-d');
            } catch (Exception $e) {
                // 日付生成エラーを握りつぶし日付検索条件を無視して検索続行
                return $query;
            }
            /* @var Builder $query */
            return $query->where(
                DB::raw("DATE_FORMAT($column, '%m%d')"),
                $operator,
                DB::raw("DATE_FORMAT('$dateFormatted', '%m%d')"),
            );
        };
    }

    /**
     * 部分一致検索
     * WHERE ${column} like %${value}%
     * @param  string  $column
     * @return Closure
     */
    public static function partialMatch(string $column): callable
    {
        return static function ($query, $value) use ($column) {
            /* @var Builder $query */
            return $query->where(static function ($query) use ($column, $value) {
                foreach (self::explodeSearchText($value) as $v) {
                    $query->where($column, 'like', '%'.preg_replace('/([\\\\_%])/', '\\\\$1', $v).'%');
                }

                return $query;
            });
        };
    }

    /**
     * WHERE ${column} like %${value}%
     * @param  array<string> $columns
     * @return Closure
     */
    public static function freeWord(array $columns): callable
    {
        return static function ($query, $value) use ($columns) {
            /* @var Builder $query */
            return $query->where(
                static function ($query) use ($columns, $value) {
                    foreach ($columns as $column) {
                        /* @var Builder $query */
                        $query = $query->orWhere($column, 'like', '%'.preg_replace('/([\\\\_%])/', '\\\\$1', $value).'%');
                    }

                    return $query;
                }
            );
        };
    }

    /**
     * WHERE MATCH${columns} like AGAINST(${value} IN BOOLEAN MODE)
     * @param  array<string> $columns
     * @return Closure
     */
    public static function fullTextSearch(array $columns): callable
    {
        return static function ($query, $value) use ($columns) {
            $charList   = [];
            $charsList  = [];
            foreach (self::explodeSearchText($value) as $str) {
                mb_strlen($str) === 1
                    ? ($charList[] = $str)
                    : ($charsList[] = "+{$str}");
            }
            /* @var Builder $query */
            return $query->where(static function ($query) use ($charsList, $charList, $columns) {
                foreach ($charList as $c) {
                    $query = self::freeWord($columns)($query, $c);
                }
                if (count($charsList) === 0) {
                    return $query;
                }

                $columnsStr = implode(',', $columns);
                /* @var Builder $query */
                return $query->whereRaw("MATCH({$columnsStr}) AGAINST(? IN BOOLEAN MODE)", [implode(' ', $charsList)]);
            });
        };
    }

    /**
     * 全角半角問わず空白で文字列を分割
     * @param  int|string $value
     * @return array
     */
    private static function explodeSearchText(int | string $value): array
    {
        $value = (string) $value;
        $value = str_replace('　', ' ', $value);
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value);

        return explode(' ', $value);
    }

    public static function dayOfWeek($column): callable
    {
        return static function ($query, $value) use ($column) {
            /* @var Builder $query */
            return $query->where(\DB::raw("DAYOFWEEK({$column})"), '=', $value);
        };
    }

    public static function hourMinute($column): callable
    {
        return static function ($query, $value) use ($column) {
            /* @var Builder $query */
            return $query->where(\DB::raw("TIME_FORMAT({$column}, '%H:%i')"), '=', $value);
        };
    }
}
