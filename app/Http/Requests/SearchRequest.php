<?php

namespace App\Http\Requests;

use App\Services\Search\Abstracts\BaseSearchService;

/**
 * 検索サービス用リクエスト
 *
 * @see BaseSearchService
 * Class SearchRequest
 * @package App\Http\Requests
 */
class SearchRequest
{
    /**
     * 検索クエリ
     * @var array { [key: string]: string }
     */
    public $search;
    /**
     * 並び替えクエリ
     * @var array|string { [key: string]: 'asc'|'desc' }
     */
    public $orderBy;
    /**
     * ページ番号
     * @var int
     */
    public $page;
    /**
     * 1ページあたりの件数
     * @var int
     */
    public $perPage;

    /**
     * SearchRequest constructor.
     */
    public function __construct()
    {
        $this->search  = is_array(request()->search) ? request()->search : [];
        $this->orderBy = request()->orderBy ?? [];
        $this->page    = request()->page;
        $this->perPage = request()->perPage;
    }
}
