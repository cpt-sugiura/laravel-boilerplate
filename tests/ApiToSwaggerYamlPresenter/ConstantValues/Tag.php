<?php

namespace Tests\ApiToSwaggerYamlPresenter\ConstantValues;

use RuntimeException;

class Tag
{
    public string $name;
    public string $description;

    /**
     * key で始まる URL は value の情報に割り当てられる.
     *
     * <code>URL前置詞 => [タグ名, タグ説明]</code>
     */
    public const TAGS = [
        Url::API_TYPE_USER => [
            '/api/api-token'         => ['auth', '認証'],
            '/api/login'             => ['auth', '認証'],
            '/api/logout'            => ['auth', '認証'],
            '/api/reset'             => ['auth', '認証'],
            '/api/token-validate'    => ['auth', '認証'],
            '/api/member'            => ['member', '会員'],
        ],
        Url::API_TYPE_CONST => [
            '/const' => ['const', 'サーバ内マスター情報'],
        ],
    ];

    /**
     * Tag constructor.
     * @param $name
     * @param $description
     */
    public function __construct(string $name, string $description)
    {
        $this->name        = $name;
        $this->description = $description;
    }

    /**
     * @param  string $apiType
     * @return string
     * @see Url::API_TYPE_USER
     */
    public static function getTagDefineStr(string $apiType): string
    {
        $indent         = '  ';
        $tagStrArr      = ['tags:'];
        $alreadyDefined = [];
        foreach (self::TAGS[$apiType] as $define) {
            if (in_array($define[0], $alreadyDefined, true)) {
                continue;
            }
            $tagStrArr[]      = $indent.'- name: '.$define[0];
            $tagStrArr[]      = $indent.$indent.'description: '.$define[1];
            $alreadyDefined[] = $define[0];
        }

        return implode("\n", $tagStrArr);
    }

    /**
     * @param  string     $fullUrl
     * @return array<Tag>
     */
    public static function getTags(string $fullUrl): array
    {
        $currentTag      = [];
        $rootRelativeUrl = str_replace(config('app.url'), '', $fullUrl);
        /** @var array $flatTags キーを壊すとURLが破壊されるのでこの形 */
        $flatTags = array_merge(
            self::TAGS[Url::API_TYPE_USER],
            self::TAGS[Url::API_TYPE_CONST],
        );
        foreach ($flatTags as $urlPrefix => $define) {
            $routePattern = '#\A(/api/?)?'.$urlPrefix.'#';
            if (preg_match_all($routePattern, $rootRelativeUrl)) {
                $currentTag[] = new self($define[0], $define[1]);
                break;
            }
        }
        if ($currentTag === []) {
            $errMsg = "タグの名前定義が取得できませんでした。\n"
                ."おそらく'.self::class.'::TAGSにURLが足りません。\n"
                ."テストしたURL: {$fullUrl}";
            throw new RuntimeException($errMsg);
        }

        return $currentTag;
    }
}
