<?php

namespace App\Http\Controllers\AdminBrowserAPI\Admin;

use App\Http\Controllers\AdminBrowserAPI\BaseAdminBrowserAPIController;
use App\Http\Presenters\PaginatorPresenter;
use App\Http\Requests\SearchRequest;
use App\Models\Search\AdminAPI\Admin\AdminSearchQueryBuilder;
use Illuminate\Http\JsonResponse;

class AdminSearchController extends BaseAdminBrowserAPIController
{
    public function __invoke(SearchRequest $request): JsonResponse
    {
        $result = (new AdminSearchQueryBuilder())
            ->search($request->search, $request->orderBy)
            ->paginate($request->perPage);

        return $this->makeResponse((new PaginatorPresenter($result))->toArray());
    }
}
