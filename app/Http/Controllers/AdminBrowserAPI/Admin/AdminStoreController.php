<?php

namespace App\Http\Controllers\AdminBrowserAPI\Admin;

use App\Http\Controllers\AdminBrowserAPI\BaseAdminBrowserAPIController;
use App\Http\Controllers\AdminBrowserAPI\Presenters\AdminPresenter;
use App\Http\Requests\AdminAPI\Admin\AdminStoreRequest;
use App\Models\Eloquents\Admin\Admin;
use Illuminate\Http\JsonResponse;

class AdminStoreController extends BaseAdminBrowserAPIController
{
    /**
     * @param  AdminStoreRequest $request
     * @return JsonResponse
     */
    public function __invoke(AdminStoreRequest $request): JsonResponse
    {
        $admin           = new Admin();
        $validated       = $request->validated();
        $admin->password = Admin::makePassword($validated['password']);

        $success = $admin->fill($validated)->save();

        return $success
            ? $this->makeResponse(new AdminPresenter($admin), '管理者を作成しました。')
            : $this->makeErrorResponse('管理者の作成に失敗しました。');
    }
}
