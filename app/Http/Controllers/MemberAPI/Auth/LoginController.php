<?php

namespace App\Http\Controllers\MemberAPI\Auth;

use App\Http\Controllers\MemberAPI\BaseMemberController;
use App\Http\HttpStatus;
use App\Http\Presenters\MemberAPI\MemberPresenter;
use App\Http\Requests\MemberAPI\Auth\LoginRequest;
use App\Http\Requests\MemberAPI\Auth\LogoutRequest;
use App\Models\Eloquents\Member\Member;
use App\Models\Eloquents\Member\MemberDeviceToken;
use Auth;
use Exception;
use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class LoginController extends BaseMemberController
{
    use Throttlesogins;
    /** @var string ログインIDは合っていると確認されるなど、メッセージの違いからセキュリティミスが起きない様に定数化 */
    protected const LOGIN_FAILED_MSG = 'ログイン情報が登録されていません';

    /**
     * 認証時に使用するガードを取得します。
     *
     * @return TokenGuard|Guard|StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('member_api');
    }

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
            $this->throwErrorResponse('ログイン情報が登録されていません', HttpStatus::UNAUTHORIZED);
            $this->incrementLoginAttempts($request);
        }
        $apiTokenName = Member::AUTH_TOKEN_NAME;
        $apiToken     = (isset($member->$apiTokenName) && is_string($member->$apiTokenName))
            ? $member->$apiTokenName
            : $member->resetApiToken();
        if (is_string($request->deviceToken)) {
            $deviceToken = MemberDeviceToken::whereMemberId($member->getKey())
                ->whereDeviceToken($request->deviceToken)
                ->first();
            $deviceToken               = $deviceToken ?? new MemberDeviceToken();
            $deviceToken->member_id    = $member->getKey();
            $deviceToken->device_token = $request->deviceToken;
            $deviceToken->save();
        }

        return $this->makeResponse([
            'member'    => new MemberPresenter($member),
            'authToken' => $apiToken,
        ]);
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
            $this->throwErrorResponse(self::LOGIN_FAILED_MSG, HttpStatus::UNAUTHORIZED);
        }

        return $member;
    }

    /**
     * ログアウト処理
     * TokenGuard に logout メソッドはない。 session も使わない、ということで \Illuminate\Foundation\Auth\AuthenticatesUsers::logout を無視
     * @param  LogoutRequest $request
     * @throws Exception
     * @return JsonResponse
     */
    public function logout(LogoutRequest $request): JsonResponse
    {
        /** @var Member $member */
        $member                = $this->guard()->user();
        $apiTokenName          = Member::AUTH_TOKEN_NAME;
        $member->$apiTokenName = null;
        $member->save();
        if (is_string($request->deviceToken)) {
            MemberDeviceToken::whereMemberId($member->getKey())
                ->whereDeviceToken($request->deviceToken)
                ->get()
                ->each(static fn (MemberDeviceToken $dToken) => $dToken->delete());
        }

        return $this->makeSuccessResponse('ログアウトしました。');
    }
}
