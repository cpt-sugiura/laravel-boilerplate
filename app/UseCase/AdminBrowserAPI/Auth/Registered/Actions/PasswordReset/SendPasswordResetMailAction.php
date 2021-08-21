<?php

namespace App\UseCase\AdminBrowserAPI\Auth\Registered\Actions\PasswordReset;

use App\UseCase\AdminBrowserAPI\Auth\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use Log;

class SendPasswordResetMailAction
{
    public function __invoke(string $email): void
    {
        $result = (new Password())->getBroker()->sendResetLink(compact('email'));

        $result === PasswordBroker::RESET_LINK_SENT
            ? Log::info('パスワードリセット用メール送信に成功。 email: '.$email)
            : Log::info('パスワードリセット用メール送信に失敗。 email: '.$email.' error: '.trans($result));
    }
}
