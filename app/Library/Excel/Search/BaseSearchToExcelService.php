<?php

namespace App\Library\Excel\Search;

use App\Library\Excel\CollectionToExcelService;
use App\Library\Search\Abstracts\BaseSearchService;

/**
 * 検索サービスをExcelファイルに適した形にするためのサービスの抽象クラス
 * Class BaseSearchToExcelService
 * @package App\Library\Excel\Search
 */
abstract class BaseSearchToExcelService
{
    /**
     * Excelファイルにする対象の検索サービス
     * @return BaseSearchService
     */
    abstract protected function searchService(): BaseSearchService;

    /**
     * 検索対象カラム
     * @return array
     */
    abstract protected function select(): array;

    /**
     * Excelのヘッダ定義. nullを返すとヘッダなし
     * @return array|null
     */
    abstract protected function header(): ?array;

    /**
     * Excelに表示する値の変換。0,1を無効,有効にしたりなどを想定
     * @param  object $searchResultItem
     * @return array
     */
    abstract protected function presenter($searchResultItem): array;

    /**
     * 検索処理。結果を任意のExcelに適したモノにする。
     * @param  array                    $search
     * @param  array                    $order
     * @return CollectionToExcelService
     */
    public function search(array $search = [], $order = []): CollectionToExcelService
    {
        $items = $this->searchService()
            ->select($this->select())
            ->search($search, $order)
            ->get()
            ->map(function ($item) {
                return $this->presenter($item);
            });

        return new CollectionToExcelService($items, $this->header());
    }
}
