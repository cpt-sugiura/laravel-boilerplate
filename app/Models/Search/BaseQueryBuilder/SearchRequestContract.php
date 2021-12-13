<?php

namespace App\Models\Search\BaseQueryBuilder;

interface SearchRequestContract
{
    /**
     * 検索クエリ
     * @return array { [key: string]: string }
     */
    public function getSearch(): array;

    /**
     * 並び替えクエリ
     * @return array|string { [key: string]: 'asc'|'desc' }
     */
    public function getOrderBy(): array | string;

    /** ページ番号 */
    public function getPage(): int;

    /** 1ページあたりの件数 */
    public function getPerPage(): int;
}
