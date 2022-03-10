<?php

namespace App\Http\Controllers\MemberAPI\Auth;

use App\Http\Controllers\MemberAPI\BaseMemberController;
use App\Http\HttpStatus;
use App\Http\Requests\MemberAPI\Auth\LoginRequest;
use App\Models\Eloquents\Member\Member;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class LoginController extends BaseMemberController
{
    use ThrottlesLogins;

    /**
     * Get the throttle key for the given request.
     *
     * @param  Request $request
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower('member|'.$request->input('api_token')).'|'.$request->ip();
    }

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */
    public function maxAttempts(): int
    {
        return 15;
    }

    /**
     * ログイン処理.
     * ID, パスワードの認証を行い, Authトークンを返す. これはログイン状態を示すためのトークンになる.
     *
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $seconds = $this->limiter()->availableIn($this->throttleKey($request));

            return $this->makeErrorResponse(Lang::get('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ], HttpStatus::TOO_MANY_REQUESTS));
        }

        $member = $this->getOrFailRequestedMember($request->email);
        if (! $this->guard()->getProvider()->validateCredentials($member, ['password' => $request->password])) {
            $this->incrementLoginAttempts($request);
            $this->throwErrorResponse(trans('auth.failed'), HttpStatus::UNAUTHORIZED);
        }
        $this->guard()->login($member);

        return $this->makeSuccessResponse();
    }

    /**
     * ログインIDの存在チェック
     * @param  string $email メールアドレス or アカウント名
     * @return Member
     */
    private function getOrFailRequestedMember(string $email): Member
    {
        $member = Member::whereStatus(Member::STATE_ENABLE)
            ->whereEmail($email)
            ->first();
        if (! ($member instanceof Member)) {
            $this->throwErrorResponse('ログイン失敗', HttpStatus::UNAUTHORIZED);
        }

        return $member;
    }

    /**
     * ログアウト処理
     * @throws Exception
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->guard()->logout();

        return $this->makeSuccessResponse('ログアウトしました。');
    }
}
