<?php

namespace App\Library\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * 振り仮名にふさわしい文字列のみを通すルール
 * Class Furigana
 * @package App\Rules
 */
class Furigana implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value === '' || $value === null) {
            return true;
        }

        $hiragana    = '[\x{3041}-\x{3096}\x{309D}-\x{309F}]';
        $zenKatakana = '[\x{30A1}-\x{30FA}\x{30FD}\x{30FF}]';
        $hanKatakana = '[\x{FF66}-\x{FF9F}]';
        $symbol      = '[\x{30FC}\x{3000} ]';

        return is_string($value) && preg_match("/\A(?:${hiragana}|${zenKatakana}|${hanKatakana}|${symbol})+\z/u", $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attributeはひらがな、カタカナ、ー、半角スペース、全角スペースのみで指定してください。';
    }
}
