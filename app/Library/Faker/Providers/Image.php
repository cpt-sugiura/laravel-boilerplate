<?php

namespace App\Library\Faker\Providers;

use Faker\Provider\Base;

class Image extends Base
{
    /**
     * ダミー画像URL
     * @param  string $str
     * @return string
     */
    public function strImageUrl(string $str): string
    {
        return route('api.debug.image', base64_encode($str));
    }

    /**
     * ダミー画像パス
     * @param  string $str
     * @return string
     */
    public function strImagePath(string $str): string
    {
        return 'dummy/'.base64_encode($str);
    }
}
