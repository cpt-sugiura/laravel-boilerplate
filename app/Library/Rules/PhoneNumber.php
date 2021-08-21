<?php

namespace App\Library\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class PhoneNumber
 */
class PhoneNumber implements Rule
{
    /**
     * バリデーションの成功を判定.
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (preg_match("/\A[0-9\-]+\z/", $value)) {
            $num_len = strlen(str_replace('-', '', $value));
            if (10 <= $num_len && $num_len <= 11) {
                return true;
            }
        }

        return false;
    }

    /**
     * バリデーションエラーメッセージの取得.
     * @return string
     */
    public function message()
    {
        return ':attributeはハイフン区切りで10桁から11桁で指定してください。';
    }
}
