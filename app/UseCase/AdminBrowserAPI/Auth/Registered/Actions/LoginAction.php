<?php

namespace App\UseCase\AdminBrowserAPI\Auth\Registered\Actions;

use App\Models\Eloquents\Admin\Admin;
use App\UseCase\AdminBrowserAPI\Auth\Password;
use Auth;

class LoginAction
{
    public function __invoke(string $email, string $password, bool $remember = false): bool
    {
        assert($email !== '');
        assert($password !== '');

        /** @var Admin|null $admin */
        $admin = Admin::whereEmail($email)->first();
        if ($admin === null || ! (new Password())->verify($password, $admin->password)) {
            return false;
        }

        Auth::guard('admin_web')->login($admin, $remember);

        return true;
    }
}
