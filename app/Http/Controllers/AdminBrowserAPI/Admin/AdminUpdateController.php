<?php

namespace App\Http\Controllers\AdminBrowserAPI\Admin;

use App\Http\Controllers\AdminBrowserAPI\BaseAdminBrowserAPIController;
use App\Http\Controllers\AdminBrowserAPI\Presenters\AdminPresenter;
use App\Http\Requests\AdminAPI\Admin\AdminUpdateRequest;
use App\Models\Eloquents\Admin\Admin;
use Illuminate\Http\JsonResponse;

class AdminUpdateController extends BaseAdminBrowserAPIController
{
    /**
     * @param  AdminUpdateRequest $request
     * @param  int|string         $adminId
     * @return JsonResponse
     */
    public function __invoke(AdminUpdateRequest $request, int|string $adminId): JsonResponse
    {
        $admin     = Admin::findOrFail($adminId);
        $validated = $request->validated();
        if (isset($validated['password'])) {
            $admin->password = Admin::makePassword($validated['password']);
        }
        $success = $admin->fill($validated)->save();

        return $success
            ? $this->makeResponse(new AdminPresenter($admin), '管理者を更新しました。')
            : $this->makeErrorResponse('管理者の更新に失敗しました。');
    }
}
