<?php

namespace App\Http\Middleware;

use App\Mail\SmtpChangeableEncodingTransport\MailCharset;
use App\Mail\SmtpChangeableEncodingTransport\SmtpChangeableEncodingTransport;
use Closure;
use Illuminate\Http\Request;

class SetMailEncode
{
    public const KEY_IN_REQUEST = 'mailEncode';
    public const KEY_IN_HEADER  = 'X-MAIL-ENCODE';

    public function handle(Request $request, Closure $next)
    {
        // GET か POST か HEADER で mailEncode=${文字コード} とパラメータを渡されたら、
        // そのリクエストの中の処理では渡された形式でメールを送るようにする
        $encode = $request->get(self::KEY_IN_REQUEST);
        $encode ??= $request->post(self::KEY_IN_REQUEST);
        $encode ??= $request->header(self::KEY_IN_HEADER);
        // パラメータによって先述のメールの文字コードを変えるメソッドを呼び出す。
        $action = match ($encode) {
            'utf-8'       => static fn () => SmtpChangeableEncodingTransport::$charset = MailCharset::UTF_8,
            'iso-2022-jp' => static fn () => SmtpChangeableEncodingTransport::$charset = MailCharset::ISO_2022_JP,
            default       => static fn () => null
        };
        $action();

        return $next($request);
    }
}
