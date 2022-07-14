%%php%%

use App\Http\Controllers\{{ $domain }}BrowserAPI\{{ $classBaseName }}\{{ $classBaseName }}DeleteController;
use App\Http\Controllers\{{ $domain }}BrowserAPI\{{ $classBaseName }}\{{ $classBaseName }}SearchController;
use App\Http\Controllers\{{ $domain }}BrowserAPI\{{ $classBaseName }}\{{ $classBaseName }}ShowController;
use App\Http\Controllers\{{ $domain }}BrowserAPI\{{ $classBaseName }}\{{ $classBaseName }}StoreController;
use App\Http\Controllers\{{ $domain }}BrowserAPI\{{ $classBaseName }}\{{ $classBaseName }}UpdateController;

Route::name('{{ \Str::snake($classBaseName) }}.')->prefix('{{ \Str::snake($classBaseName) }}')->group(static function () {
    Route::get('', {{ $classBaseName }}SearchController::class)->name('search');
    Route::post('', {{ $classBaseName }}StoreController::class)->name('store');
    Route::get('{{ "{". \Str::camel($primaryKey) ."}" }}', {{ $classBaseName }}ShowController::class)->name('show');
    Route::post('{{ "{". \Str::camel($primaryKey) ."}" }}', {{ $classBaseName }}UpdateController::class)->name('update');
    Route::delete('{{ "{". \Str::camel($primaryKey) ."}" }}', {{ $classBaseName }}DeleteController::class)->name('delete');
});
