<?php

namespace App\Models\Search\BaseQueryBuilder;

use App\Models\Search\BaseQueryBuilder\Abstracts\SearchableOrderByContract;
use App\Models\Search\BaseQueryBuilder\Abstracts\SearchableWhereContract;
use App\Models\Search\BaseQueryBuilder\Abstracts\SearchOrderBy;
use App\Models\Search\BaseQueryBuilder\Abstracts\SearchWhere;
use Closure;
use DateTimeInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseSearchQueryBuilder
{
    /**
     * @var Builder
     */
    public Builder $query;
    /** 検索のデフォルト値として扱う */
    private SearchRequestContract $searchRequestDefault;

    /**
     * BaseSearchService constructor.
     */
    public function __construct()
    {
        $this->query = $this->from();
    }

    /**
     * 検索結果として表示するカラム
     *
     * A as B もOK.
     * @return array<string|Expression>
     */
    abstract protected function select(): array;

    /**
     * 検索対象を定義. 主にfrom, join, related, group by
     * @return Builder
     */
    abstract protected function from(): Builder;

    /**
     * 検索対象カラムとリクエストのマッピング
     * WHERE 句の指定をします。
     * 次の動作をします.
     * - string: WHERE $value = $search[$key]
     * - array : WHERE $value[0] $value[1] $search[$key]]
     * - callable : (Builder $query, $value) => $query な Laravel クエリビルダ動作
     * - SearchableWhereContract : $value->buildQuery(Builder $query, $value)
     *
     * SearchWhereMacro クラスによく使う少し特殊な WHERE 句のクロージャを返す静的メソッド群が配置してあります。
     * @return array<SearchableWhereContract|string|Expression|array<string|Expression>|callable>
     * @see \App\Models\Search\BaseQueryBuilder\Macros\SearchWhereMacro
     */
    abstract protected function searchableWhereFields(): array;

    /**
     * 並び替え可能カラムとリクエストのマッピング
     * @return array<SearchableOrderByContract>
     */
    abstract protected function orderByAbleFields(): array;

    /**
     * デフォルトの並び順になるOrderBy句の記述を想定
     * @return Closure
     */
    protected function defaultOrderBy(): callable
    {
        return static function (Builder $query) {
            return $query;
        };
    }

    /**
     * 検索クエリを構築.
     *
     *     $search = ['name' => 'hoge', 'updatedAtStart' => new Carbon('2020-01-04')];
     *     $orderBy = ['updatedAt' => 'desc', 'name' => 'asc'];
     *
     * @param  array|null             $search
     * @param  array|string|null      $orderBy
     * @return BaseSearchQueryBuilder
     */
    public function search(?array $search = null, null | array | string $orderBy = null): self
    {
        $search ??= isset($this->searchRequestDefault) ? $this->searchRequestDefault->getSearch() : [];
        $orderBy ??= isset($this->searchRequestDefault) ? $this->searchRequestDefault->getOrderBy() : [];

        $this->query->select($this->select());
        $this->buildSearchWhere($search);
        if ($orderBy !== null) {
            $this->buildSearchOrderBy($orderBy);
        }
        $this->query = $this->defaultOrderBy()->__invoke($this->query);

        return $this;
    }

    /**
     * ex.
     * $search = ['name' => 'hoge', 'id' => 3];
     *
     * @param  array   $search
     * @return Builder
     */
    protected function buildSearchWhere(array $search): Builder
    {
        if (empty($search)) {
            return $this->query;
        }

        $searchableFields = $this->searchableWhereFields();
        foreach ($search as $key => $value) {
            if (array_key_exists($key, $searchableFields)) {
                $where = $searchableFields[$key];
                if (! ($where instanceof SearchableWhereContract)) {
                    $where = new SearchWhere($where);
                }
                $this->query = $where->buildQuery($this->query, $value);
            }
        }

        return $this->query;
    }

    /**
     * @param  array|string $orderBy
     * @return Builder
     */
    protected function buildSearchOrderBy(array | string $orderBy): Builder
    {
        if (is_string($orderBy)) {
            $orderBy = [$orderBy => 'asc'];
        }
        if (count($orderBy)) {
            $orderByAbleColumns = $this->orderByAbleFields();
            foreach ($orderBy as $key => $direction) {
                if (array_key_exists($key, $orderByAbleColumns)) {
                    if (! ($orderByAbleColumns[$key] instanceof SearchableOrderByContract)
                        && (is_string($orderByAbleColumns[$key]) || $orderByAbleColumns[$key] instanceof Expression)
                    ) {
                        $orderByAbleColumns[$key] = new SearchOrderBy($orderByAbleColumns[$key]);
                    }
                    $this->query = $orderByAbleColumns[$key]->buildQuery($this->query, $direction);
                }
            }
        }

        return $this->query;
    }

    /**
     * 検索結果をいい感じの構造体にする
     * @param  object       $searchResultItem
     * @return object|array
     */
    protected function formatter(object $searchResultItem): object | array
    {
        foreach (get_object_vars($searchResultItem) as $property => $v) {
            $date = date_create_from_format('Y-m-d H:i:s', $searchResultItem->$property, )
                ?: date_create_from_format('Y-m-d', $searchResultItem->$property);
            if ($date instanceof \DateTime) {
                $searchResultItem->$property = $date->format(DateTimeInterface::ATOM);
            }
        }

        return $searchResultItem;
    }

    /**
     * 検索結果全取得のラッパー。構造体への変換フォーマットを使う
     * @param  string[]   $columns
     * @return Collection
     */
    public function get(array $columns = ['*']): Collection
    {
        return $this->query->get($columns)
            ->map(fn ($item) => $this->formatter($item));
    }

    /**
     * ページネーションのラッパー。構造体への変換フォーマットを使う
     * @param  int|null                                              $perPage  デフォルトは 15
     * @param  string[]                                              $columns
     * @param  string                                                $pageName
     * @param  null                                                  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int | null $perPage = null, array $columns = null, string $pageName = 'pages', $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $columns ??= $this->select() ?? ['*'];
        $perPage ??= isset($this->searchRequestDefault) ? $this->searchRequestDefault->getPerPage() : 15;
        $page ??= isset($this->searchRequestDefault) ? $this->searchRequestDefault->getPage() : null;
        if (! isset($page)) {
            try {
                $page = request()->page;
            } catch (BindingResolutionException $e) {
                // コンソールで呼ばれた場合にありうる。この場合は握りつぶす
            }
        }

        /** @var LengthAwarePaginator $paginate */
        $paginate = $this->query->paginate($perPage, $columns, $pageName, $page);
        $items    = collect($paginate->items())->map(fn ($item) => $this->formatter($item));
        $paginate->setCollection($items);

        return $paginate;
    }

    /**
     * 総数抜きページネーションのラッパー。構造体への変換フォーマットを使う
     * @param  int|null  $perPage  デフォルトは 15
     * @param  string[]  $columns
     * @param  string    $pageName
     * @param  null      $page
     * @return Paginator
     */
    public function simplePaginate(int | null $perPage = null, ?array $columns = null, string $pageName = 'pages', $page = null): Paginator
    {
        $columns ??= $this->select();
        $perPage ??= isset($this->searchRequestDefault) ? $this->searchRequestDefault->getPerPage() : 15;
        $page ??= isset($this->searchRequestDefault) ? $this->searchRequestDefault->getPage() : null;
        if (! isset($page)) {
            try {
                $page = request()->page;
            } catch (BindingResolutionException $e) {
                // コンソールで呼ばれた場合にありうる。この場合は握りつぶす
            }
        }

        /** @var LengthAwarePaginator $paginate */
        $paginate = $this->query->simplePaginate($perPage, $columns, $pageName, $page);
        $items    = collect($paginate->items())->map(fn ($item) => $this->formatter($item));
        $paginate->setCollection($items);

        return $paginate;
    }

    public function setSearchRequestAsDefault(SearchRequestContract $searchRequestContract): static
    {
        $this->searchRequestDefault = $searchRequestContract;

        return $this;
    }
}
