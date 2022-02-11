<?php

namespace App\Providers;

use App\Mail\SmtpChangeableEncodingTransport\SmtpChangeableEncodingTransportFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;

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
        // smtp の名前を使っているメールドライバーを上書き
        // もし上書きしないのであれば、ここの第一引数を別の名前にして /config/mail.php に
        // 'mailers' => [
        //     '並列させる新たなMailerの名前' => [
        //         'transport'  => 'ここで定めた新たな transport の名前'
        //     ],
        //     'smtp' => [
        //          // 省略
        // の様に書く
        \Mail::extend('smtp', static function() {
            // smtp ドライバーの初期化処理をコピペ。本来の smtp ドライバー用の TransportFactory の代わりに
            // 独自の SmtpIso2022JpTransportFactory を呼び出す
            $config = config('mail.mailers.smtp');
            return (new \App\Mail\SmtpChangeableEncodingTransport\SmtpChangeableEncodingTransportFactory())
                ->create(new \Symfony\Component\Mailer\Transport\Dsn(
                    !empty($config['encryption']) && $config['encryption'] === 'tls' ? 'smtps' : '',
                    $config['host'],
                    $config['username'] ?? null,
                    $config['password'] ?? null,
                    $config['port'] ?? null,
                    $config
                ));
        });
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
