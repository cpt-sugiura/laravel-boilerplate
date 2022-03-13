<?php

use App\Http\Controllers\AdminBrowserAPI\Auth\LoginController;

Route::name('auth.')->group(static function () {
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware('auth:admin_web')->where([
    'adminId' => '\d+',
])->group(static function () {
    include __DIR__.'/admin_browser_api/admin.php';
});
