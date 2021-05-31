<?php

namespace App\Http\Controllers\MemberAPI\Auth;

use App\Http\Controllers\MemberAPI\BaseMemberController;
use App\Models\Eloquents\MemberApiToken;
use Illuminate\Http\JsonResponse;

class ApiTokenController extends BaseMemberController
{
    /**
     * APIトークンの取得
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->makeResponse(['apiToken' => MemberApiToken::create()->token]);
    }
}
