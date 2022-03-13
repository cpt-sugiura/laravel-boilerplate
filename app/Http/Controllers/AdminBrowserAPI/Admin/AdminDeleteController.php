<?php

namespace App\Http\Controllers\AdminBrowserAPI\Admin;

use App\Http\Controllers\AdminBrowserAPI\BaseAdminBrowserAPIController;
use App\Http\HttpStatus;
use App\Models\Eloquents\Admin\Admin;
use Exception;
use Illuminate\Http\JsonResponse;

class AdminDeleteController extends BaseAdminBrowserAPIController
{
    /**
     * @param int|string $adminId
     *@throws Exception
     * @return JsonResponse
     */
    public function __invoke(int|string $adminId): JsonResponse
    {
        $admin   = Admin::findOrFail($adminId);
        if (Admin::count() === 1) {
            return $this->makeErrorResponse('最後の管理者は削除できません。', HttpStatus::UNPROCESSABLE_ENTITY);
        }
        $success = $admin->delete();

        return $success
            ? $this->makeSuccessResponse('管理者を削除しました。')
            : $this->makeSuccessResponse('管理者の削除に失敗しました。');
    }
}
