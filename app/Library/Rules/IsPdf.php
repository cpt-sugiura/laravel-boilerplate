<?php

namespace App\Library\Rules;

use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class IsPdf implements Rule
{
    public function passes($attribute, $value)
    {
        if ($value === null) {
            return true;
        }
        if (! ($value instanceof UploadedFile)) {
            return false;
        }

        return str_starts_with($value->openFile()->fgets(), '%PDF-');
    }

    /**
     * バリデーションエラーメッセージの取得.
     * @return string
     */
    public function message()
    {
        return 'ファイルは PDF である必要があります。';
    }

    public function __toString()
    {
        return 'PDFファイル';
    }
}
