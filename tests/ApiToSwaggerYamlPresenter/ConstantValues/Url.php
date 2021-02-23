<?php

namespace Tests\ApiToSwaggerYamlPresenter\ConstantValues;

use RuntimeException;
use Str;

class Url
{
    public const ROUTING = [
        '/api/api-token'                                               => 'APIトークン取得',
        '/api/token-validate'                                          => 'トークン検証',
        '/api/login'                                                   => 'ログイン',
        '/api/logout'                                                  => 'ログアウト',
        '/api/reset'                                                   => 'パスワードリセット',
        '/api/member/edit'                                             => 'プロフィール更新',
        '/api/member/password'                                         => 'パスワード変更',
        '/api/member/{memberId}'                                       => '会員詳細',
        '/api/member'                                                  => '会員検索',
        // const関連
        '/api/const/pref-names' => '都道府県名一覧',
        '/api/const/genders'    => '性別一覧',
    ];

    public static function getUrl(string $fullUrl): array
    {
        $currentRoute    = [null, null];
        $rootRelativeUrl = str_replace(config('app.url'), '', $fullUrl);
        foreach (self::ROUTING as $url => $summary) {
            $routePattern = '#\A.*'.preg_replace('/{\w+}/', '\w+', $url).'\z#';
            if (preg_match_all($routePattern, $rootRelativeUrl)) {
                // 上の方から読んでいって最初に合致するルーティングがあったらそれを得て処理を打ち切り
                // 複数合致するルーティングがある場合は↑を優先
                $currentRoute = [$url, $summary];
                break;
            }
        }
        if ($currentRoute === [null, null]) {
            $errMsg = "URLの名前定義が取得できませんでした。\n"
                .'おそらく'.self::class."::ROUTINGにURLが足りません。\n"
                ."テストしたURLフル: {$fullUrl}\n"
                ."テストしたURLルート相対以下: {$rootRelativeUrl}\n"
                .self::class."::ROUTINGのあるファイル:\n "
                .__FILE__;
            throw new RuntimeException($errMsg);
        }

        return $currentRoute;
    }

    public const API_TYPE_USER      = 'MEMBER_API';
    public const API_TYPE_CONST     = 'CONST';
    public const API_TYPE_DEBUG     = 'DEBUG';
    public const API_TYPE_NOT_API   = 'UNDEFINED';

    public static function getApiType(string $fullUrl): string
    {
        $domain          = Str::finish(config('app.url'), '/');
        $rootRelativeUrl = str_replace($domain, '', $fullUrl);
        if (Str::startsWith($rootRelativeUrl, ['api/debug'])) {
            return self::API_TYPE_DEBUG;
        }
        if (Str::startsWith($rootRelativeUrl, 'api/const')) {
            return self::API_TYPE_CONST;
        }
        if (Str::startsWith($rootRelativeUrl, 'api/')) {
            return self::API_TYPE_USER;
        }

        return self::API_TYPE_NOT_API;
    }
}
