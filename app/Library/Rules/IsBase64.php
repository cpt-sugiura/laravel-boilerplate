<?php

namespace App\Library\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 渡された文字列がRFCで定義されている素朴なBase64であるならば合格
 * @see https://gist.github.com/nasrulhazim/f3ca4c231216a491b423e06e069473ae
 */
class IsBase64 implements Rule
{
    /**
     * バリデーションの成功を判定.
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 値をデータ形式宣言部とデータ本体に分割
        $explode = explode(',', $value);
        $allow   = ['png', 'jpg', 'jpeg', 'gif'];
        $format  = str_replace(
            ['data:image/', ';', 'base64'],
            '',
            $explode[0]
        );
        if (! in_array($format, $allow, true)) {
            return false;
        }
        if (! preg_match('%\A[a-zA-Z0-9/+]*={0,2}\z%', $explode[1])) {
            return false;
        }

        return true;
    }

    /**
     * バリデーションエラーメッセージの取得.
     * @return string
     */
    public function message()
    {
        return '画像はbase64形式のpng, jpg, gif情報のいずれかである必要があります。';
    }

    public function __toString()
    {
        return 'Base64文字列';
    }
}
