<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        setlocale(LC_ALL, 'C.UTF-8');
        if(config('app.debug') && config('app.debug_datetime')) {
            $debugDatetime = config('app.debug_datetime');
            // @see https://regexper.com/#%5E%28%5Cd%7B1%2C4%7D%29%28%3F%3A-%28%5Cd%7B2%7D%29%28%3F%3A-%28%5Cd%7B2%7D%29%28%3F%3A%20%28%5Cd%7B2%7D%29%28%3F%3A%3A%28%5Cd%7B2%7D%29%28%3F%3A%3A%28%5Cd%7B2%7D%29%29%3F%29%3F%29%3F%29%3F%29%3F%24
            if(!preg_match('/^(\d{1,4})(?:-(\d{2})(?:-(\d{2})(?: (\d{2})(?::(\d{2})(?::(\d{2}))?)?)?)?)?$/', $debugDatetime, $matches)) {
                throw new \LogicException('APP_DEBUG_DATETIME は Y-m-d H:i:s 形式に前方一致する形で設定してください。');
            }
            $y = $matches[1];
            $m = $matches[2] ?? date('m');
            $d = $matches[3] ?? date('d');
            $h = $matches[4] ?? date('H');
            $i = $matches[5] ?? date('i');
            $s = $matches[6] ?? date('s');
            Carbon::setTestNow($y . '-' . $m . '-' . $d . ' ' . $h . ':' . $i . ':' . $s);
        }
    }
}
