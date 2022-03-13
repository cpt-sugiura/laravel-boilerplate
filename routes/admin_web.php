<?php

use App\Http\Controllers\AdminWeb\Auth\LoginController;
use App\Http\Controllers\AdminWeb\IndexController;
use App\Models\Eloquents\Admin\Admin;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

Route::get('', IndexController::class);
Route::middleware('auth:admin_web')->group(static function () {
    Route::get('storage/{any}', static function ($any) {
        $contentType = preg_match('/\.css$/', $any) ? 'text/css; charset=UTF-8' : null;
        $headers     = [];
        if ($contentType) {
            $headers['Content-Type'] = $contentType;
        }

        if (Storage::disk(Admin::ASSETS_STORAGE_DISK_KEY)->exists($any)) {
            return Storage::disk(Admin::ASSETS_STORAGE_DISK_KEY)->response($any, null, $headers);
        }
        abort(404);
    })->where('any', '.*')->name('storage');

    Route::any('', IndexController::class);
    Route::any('{any}', IndexController::class)->where('any', '.*');
});
