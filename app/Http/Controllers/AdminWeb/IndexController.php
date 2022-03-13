<?php

namespace App\Http\Controllers\AdminWeb;

use App\Http\Controllers\AdminBrowserAPI\Presenters\AdminPresenter;
use App\Http\Controllers\Controller;
use App\Models\Eloquents\Admin\Admin;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class IndexController extends Controller
{
    public function __invoke(): Factory|View|Application
    {
        if (\Auth::guard('admin_web')->check()) {
            /** @var Admin $admin */
            $admin       = \Auth::guard('admin_web')->user();
            $presenter   = new AdminPresenter($admin);
            $adminJson   = $presenter->toJson();
        }

        return view('admin.index', [
            'adminJson' => $adminJson ?? '',
        ]);
    }
}
