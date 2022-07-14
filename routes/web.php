<?php

use App\Http\Controllers\ClientErrorLoggerController;

Route::get('/', static function () {
    return 'インデックスページ';
});
Route::post('/api/logging/error', ClientErrorLoggerController::class)->name('logging.error');
