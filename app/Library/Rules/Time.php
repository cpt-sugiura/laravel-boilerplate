<?php

namespace App\Library\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class Time
 */
class Time implements Rule
{
    /**
     * バリデーションの成功を判定.
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (preg_match("/\A(0?\d|1\d|2[0-3]):[0-5]?\d\z/", $value)) {
            return true;
        }

        return false;
    }

    /**
     * バリデーションエラーメッセージの取得.
     * @return string
     */
    public function message()
    {
        return ':attributeは00:00~23:59の間で指定してください。';
    }
}
