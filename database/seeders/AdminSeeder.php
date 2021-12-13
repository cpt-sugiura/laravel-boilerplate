<?php

namespace Database\Seeders;

use App\Models\Eloquents\Admin\Admin;

class AdminSeeder extends BaseSeeder
{
    public function run(): void
    {
        if (! Admin::query()->whereEmail('admin@example.com')->exists()) {
            $admin           = new Admin();
            $admin->name     = 'テスト管理者';
            $admin->email    = 'admin@example.com';
            $admin->password = (new \App\UseCase\AdminBrowserAPI\Auth\Password())
                ->hash('admin');
            $admin->save();
        }
    }
}
