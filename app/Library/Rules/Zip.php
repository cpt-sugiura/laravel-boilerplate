<?php

namespace App\Library\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class Zip
 * @package App\Rules
 */
class Zip implements Rule
{
    /**
     * バリデーションの成功を判定.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (preg_match("/\A\d{3}-?\d{4}\z/", $value)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attributeは7桁の数字で指定してください。';
    }
}
