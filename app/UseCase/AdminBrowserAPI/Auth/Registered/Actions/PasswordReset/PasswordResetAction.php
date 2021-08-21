<?php

namespace App\UseCase\AdminBrowserAPI\Auth\Registered\Actions\PasswordReset;

use App\Models\Eloquents\Admin\Admin;
use App\UseCase\AdminBrowserAPI\Auth\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Str;

class PasswordResetAction
{
    public function __invoke(string $email, string $password, string $token): bool
    {
        return PasswordBroker::PASSWORD_RESET === (new Password())->getBroker()->reset(
            compact('email', 'password', 'token'),
            function ($account, $password) {
                $this->resetPassword($account, $password);
            }
        );
    }

    /**
     * リセットしたパスワードをアカウントに保存
     *
     * @param  Admin   $admin
     * @param  string  $password
     * @return void
     */
    protected function resetPassword(Admin $admin, string $password): void
    {
        $admin->password       = (new Password())->hash($password);
        $admin->remember_token = Str::random(60);
        $admin->save();

        event(new PasswordReset($admin));
    }
}
