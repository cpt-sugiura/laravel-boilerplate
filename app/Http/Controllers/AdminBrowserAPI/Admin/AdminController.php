<?php

namespace App\Http\Controllers\AdminBrowserAPI\Admin;

use App\Http\Controllers\AdminBrowserAPI\BaseAdminBrowserAPIController;
use App\Http\HttpStatus;
use App\Http\Presenters\PaginatorPresenter;
use App\Http\Requests\AdminAPI\Admin\AdminStoreRequest;
use App\Http\Requests\AdminAPI\Admin\AdminUpdateRequest;
use App\Http\Requests\SearchRequest;
use App\Models\Eloquents\Admin\Admin;
use App\Models\Search\AdminAPI\Admin\AdminSearchQueryBuilder;
use Exception;
use Illuminate\Http\JsonResponse;

class AdminController extends BaseAdminBrowserAPIController
{
    public function search(SearchRequest $request): JsonResponse
    {
        $result = (new AdminSearchQueryBuilder())
            ->search($request->search, $request->orderBy)
            ->paginate($request->perPage);

        return $this->makeResponse((new PaginatorPresenter($result))->toArray());
    }

    /**
     * @param  int|string   $adminId
     * @return JsonResponse
     */
    public function show($adminId): JsonResponse
    {
        $admin = Admin::findOrFail($adminId);

        return $this->makeResponse($this->adminPresenter($admin));
    }

    /**
     * @param  AdminStoreRequest $request
     * @return JsonResponse
     */
    public function store(AdminStoreRequest $request): JsonResponse
    {
        $admin           = new Admin();
        $validated       = $request->validated();
        $admin->password = Admin::makePassword($validated['password']);

        $success = $admin->fill($validated)->save();

        return $success
            ? $this->makeResponse($this->adminPresenter($admin), '管理者を作成しました。')
            : $this->makeErrorResponse('管理者の作成に失敗しました。');
    }

    /**
     * @param  AdminUpdateRequest $request
     * @param  int|string         $adminId
     * @return JsonResponse
     */
    public function update(AdminUpdateRequest $request, $adminId): JsonResponse
    {
        $admin     = Admin::findOrFail($adminId);
        $validated = $request->validated();
        if (isset($validated['password'])) {
            $admin->password = Admin::makePassword($validated['password']);
        }
        $success = $admin->fill($validated)->save();

        return $success
            ? $this->makeResponse($this->adminPresenter($admin), '管理者を更新しました。')
            : $this->makeErrorResponse('管理者の更新に失敗しました。');
    }

    /**
     * @param  int|string   $adminId
     * @throws Exception
     * @return JsonResponse
     */
    public function delete($adminId): JsonResponse
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

    private function canDelete(): bool
    {
        return Admin::count() > 1;
    }

    private function adminPresenter(Admin $admin): array
    {
        return array_merge($admin->toArray(), [
            'canDelete' => $this->canDelete(),
        ]);
    }
}
