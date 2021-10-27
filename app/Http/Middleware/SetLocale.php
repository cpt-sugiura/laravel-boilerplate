<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public const HEADER_KEY = 'X-LOCALE';

    public function handle(Request $request, Closure $next)
    {
        $this->handleWithoutLogin($request);

        return $next($request);
    }

    private function handleWithoutLogin(Request $request): void
    {
        $headerLocale = $request->header(self::HEADER_KEY);
        if (in_array($headerLocale, ['ja', 'en'], true)) {
            App::setLocale($headerLocale);
        }
    }
}
