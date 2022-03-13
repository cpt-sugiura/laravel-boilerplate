<?php

namespace App\Http\Controllers\AdminBrowserAPI\Admin;

use App\Http\Controllers\AdminBrowserAPI\BaseAdminBrowserAPIController;
use App\Http\Controllers\AdminBrowserAPI\Presenters\AdminPresenter;
use App\Models\Eloquents\Admin\Admin;
use Illuminate\Http\JsonResponse;

class AdminShowController extends BaseAdminBrowserAPIController
{
    /**
     * @param  int|string   $adminId
     * @return JsonResponse
     */
    public function __invoke(int|string $adminId): JsonResponse
    {
        $admin = Admin::findOrFail($adminId);

        return $this->makeResponse(new AdminPresenter($admin));
    }
}
