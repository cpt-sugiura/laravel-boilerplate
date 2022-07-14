<?php

namespace App\Library\Rules;

use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileExtension implements Rule
{
    public function __construct(private readonly string $ext)
    {
    }

    /**
     * バリデーションの成功を判定.
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if ($value === null) {
            return true;
        }
        if (! ($value instanceof UploadedFile)) {
            return false;
        }

        return strtolower($value->getClientOriginalExtension()) === strtolower($this->ext);
    }

    /**
     * バリデーションエラーメッセージの取得.
     * @return string
     */
    public function message(): string
    {
        return ':attributeの拡張子は'.$this->ext.'である必要があります。';
    }
}
