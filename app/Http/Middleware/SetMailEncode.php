<?php

namespace App\Http\Middleware;

use App\Mail\GlobalSwiftMailEncodeChanger;
use Closure;
use Illuminate\Http\Request;

class SetMailEncode
{
    public const KEY_IN_REQUEST = 'mailEncode';

    public function handle(Request $request, Closure $next)
    {
        // GET か POST で mailEncode=${文字コード} とパラメータを渡されたら、
        // そのリクエストの中の処理では渡された形式でメールを送るようにする
        $encode = $request->get(self::KEY_IN_REQUEST);
        $encode ??= $request->post(self::KEY_IN_REQUEST);
        // パラメータによって先述のメールの文字コードを変えるメソッドを呼び出す。
        $action = match ($encode) {
            'utf-8'       => static fn () => GlobalSwiftMailEncodeChanger::toUtf8(),
            'iso-2022-jp' => static fn () => GlobalSwiftMailEncodeChanger::toIso2022Jp(),
            default       => static fn () => null
        };
        $action();

        return $next($request);
    }
}
