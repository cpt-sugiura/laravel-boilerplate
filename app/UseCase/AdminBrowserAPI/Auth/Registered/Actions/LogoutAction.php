<?php

namespace App\UseCase\AdminBrowserAPI\Auth\Registered\Actions;

use Auth;

class LogoutAction
{
    public function __invoke()
    {
        Auth::guard('admin_web')->logout();
    }
}
