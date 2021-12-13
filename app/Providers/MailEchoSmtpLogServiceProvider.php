<?php

namespace App\Providers;

use Illuminate\Mail\Mailer;
use Illuminate\Support\ServiceProvider;
use Log;
use Swift_Plugins_Logger;
use Swift_Plugins_LoggerPlugin;

/**
 * Laravel 内のメーラーが SMTP 通信している内容をロギングするためのサービスプロバイダー
 *
 * ここ以外でこのロガーを使わない前提として、これ自身が Swift_Plugins_Logger インターフェースを満たすようにする。
 * コードを短縮できる。コードをまとめられる点で便利だが、他でのロガーが絡みだすなら分離した方が良い。
 */
class MailEchoSmtpLogServiceProvider extends ServiceProvider implements Swift_Plugins_Logger
{
    /**
     * Laravel起動時に実行される処理。ここでロガーを登録
     *
     * @return void
     */
    public function boot(): void
    {
        // Laravel のメーラーから順に送信実体である SwiftMailer を取り出す。
        /** @var Mailer $laravelMailer */
        $laravelMailer = \Mail::mailer();
        $swiftMailer   = $laravelMailer->getSwiftMailer();

        // SwiftMailerにはロガーを仕込むためのメソッドがあり、それを差し込む
        // @see https://swiftmailer.symfony.com/docs/plugins.html#logger-plugin
        // このクラス自身が Swift_Plugins_Logger を満たしているロガークラスのため $this を渡す。
        $swiftMailer->registerPlugin(new Swift_Plugins_LoggerPlugin($this));
    }

    /**
     * 通信ログが追加される度に呼び出されるメソッド
     *
     * @param string $entry
     */
    public function add($entry)
    {
        // 手っ取り早く記録するならこれだけで十分。
        // Swift_Mailer が送って来たログを Laravel としてのログに転送
        Log::info($entry);
    }

    // ↑で済ますならば不要
    public function clear()
    {
    }

    public function dump()
    {
    }
}
