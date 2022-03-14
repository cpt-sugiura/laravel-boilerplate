<?php

use App\Http\Controllers\MemberAPI\ClientErrorLoggerController;

Route::get('/', static function () {
    return 'インデックスページ';
});
Route::post('/api/logging/error', ClientErrorLoggerController::class)->name('logging.error');
