<?php

namespace App\Http\Controllers\MemberAPI\Auth;

use App\Http\Controllers\MemberAPI\BaseMemberController;
use App\Http\HttpStatus;
use App\Http\Middleware\MemberAPI\ApiTokenAuth;
use App\Models\Eloquents\Member\Member;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

class TokensVerifyController extends BaseMemberController
{
    use AuthenticatesUsers;

    public const SUCCESS_MSG              = 'ok';
    public const FAILED_BY_API_TOKEN_MSG  = 'invalid_api_token';
    public const FAILED_BY_AUTH_TOKEN_MSG = 'invalid_auth_token';

    /**
     * APIトークンとAuthトークンが正しいかチェックするAPI
     * @param  Request      $request アプリからのリクエスト
     * @return JsonResponse
     */
    public function verifyOrFailTokens(Request $request): JsonResponse
    {
        Log::info($request->bearerToken());
        (new ApiTokenAuth())->authenticate($request)
            ?: $this->throwErrorResponse(self::FAILED_BY_API_TOKEN_MSG, HttpStatus::UNAUTHORIZED);

        if (empty($request->bearerToken())) {
            $this->throwErrorResponse(self::FAILED_BY_AUTH_TOKEN_MSG, HttpStatus::UNAUTHORIZED);
        }

        $member = Member::whereAuthToken($request->bearerToken())->first();
        if ($member === null) {
            $this->throwErrorResponse(self::FAILED_BY_AUTH_TOKEN_MSG, HttpStatus::UNAUTHORIZED);
        }

        return $this->makeSuccessResponse(self::SUCCESS_MSG);
    }
}
