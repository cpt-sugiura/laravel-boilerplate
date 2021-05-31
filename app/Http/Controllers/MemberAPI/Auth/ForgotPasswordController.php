<?php

/** パスワードリセットメールを送るためのコントローラ. */

namespace App\Http\Controllers\MemberAPI\Auth;

use App\Http\Controllers\MemberAPI\BaseMemberController;
use App\Http\Requests\MemberAPI\Auth\PasswordResetRequest;
use App\UseCase\MemberAPI\Auth\Password;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

/**
 * パスワードリセットメールを送るためのコントローラ
 */
class ForgotPasswordController extends BaseMemberController
{
    use SendsPasswordResetEmails{
        sendResetLinkEmail as parentSendResetLinkEmail;
    }

    public function sendResetLinkEmail(PasswordResetRequest $request)
    {
        return $this->parentSendResetLinkEmail($request);
    }

    /**
     * パスワードリセットに成功したリンクのレスポンスを取得する.
     *
     * @param  Request      $request
     * @param  string       $response
     * @return JsonResponse
     */
    public function sendResetLinkResponse(Request $request, string $response): JsonResponse
    {
        Log::info('会員パスワードリセット用メール送信に成功。 email: '.$request->email);

        return $this->makeSuccessResponse(trans('passwords.sent'));
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        Log::info('会員パスワードリセット用メール送信に失敗。 email: '.$request->email);

        return $this->makeSuccessResponse(trans('passwords.sent'));
    }

    public function broker(): PasswordBroker
    {
        return (new Password)->getBroker();
    }
}
