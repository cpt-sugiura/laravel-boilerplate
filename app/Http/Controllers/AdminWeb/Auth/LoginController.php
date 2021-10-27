<?php

namespace App\Http\Controllers\AdminWeb\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Str;

/**
 * 管理者ログイン用コントローラ
 */
class LoginController extends Controller
{
    public function showLoginForm(): Factory | View | Application
    {
        if (is_array(session('url'))
            && ! empty(session('url')['intended'])
            && ! Str::contains(session('url')['intended'], 'login')
        ) {
            $redirectTo =  session('url')['intended'];
        } else {
            $redirectTo = '/admin';
        }

        return view('admin.auth.login', ['redirectTo' => $redirectTo]);
    }
}
