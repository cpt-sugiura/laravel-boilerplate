<?php

namespace App\Http\Controllers\AdminBrowserAPI\Auth;

use App\Http\Controllers\AdminBrowserAPI\BaseAdminBrowserAPIController;
use App\Http\HttpStatus;
use App\Http\Requests\AdminAPI\Auth\LoginRequest;
use App\UseCase\AdminBrowserAPI\Auth\Registered\Actions\LoginAction;
use App\UseCase\AdminBrowserAPI\Auth\Registered\Actions\LogoutAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;

/**
 * 管理者ログイン用コントローラ
 */
class LoginController extends BaseAdminBrowserAPIController
{
    use ThrottlesLogins;

    /**
     * Get the throttle key for the given request.
     *
     * @return string
     */
    protected function throttleKey(): string
    {
        return $this->resolveRequestSignature();
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

    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $seconds = $this->limiter()->availableIn($this->throttleKey());

            return $this->makeErrorResponse(Lang::get('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]), HttpStatus::TOO_MANY_REQUESTS);
        }
        $success = $action($request->email, $request->password, (bool) $request->remember);

        if (! $success) {
            $this->throwErrorResponse(Lang::get('auth.failed'), HttpStatus::UNAUTHORIZED);
            $this->incrementLoginAttempts($request);
        }

        return $this->makeSuccessResponse();
    }

    public function logout(LogoutAction $action): JsonResponse
    {
        $action();

        return $this->makeSuccessResponse();
    }
}
