<?php

use App\Http\Controllers\AdminBrowserAPI\Admin\AdminDeleteController;
use App\Http\Controllers\AdminBrowserAPI\Admin\AdminSearchController;
use App\Http\Controllers\AdminBrowserAPI\Admin\AdminShowController;
use App\Http\Controllers\AdminBrowserAPI\Admin\AdminStoreController;
use App\Http\Controllers\AdminBrowserAPI\Admin\AdminUpdateController;

Route::name('admin.')->prefix('admin')->group(static function () {
    Route::get('', AdminSearchController::class)->name('search');
    Route::post('', AdminStoreController::class)->name('store');
    Route::get('{adminId}', AdminShowController::class)->name('show');
    Route::post('{adminId}', AdminUpdateController::class)->name('update');
    Route::delete('{adminId}', AdminDeleteController::class)->name('delete');
});
