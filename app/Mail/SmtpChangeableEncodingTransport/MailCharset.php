<?php

namespace App\Mail\SmtpChangeableEncodingTransport;

enum MailCharset
{
    case ISO_2022_JP;
    case UTF_8;

    public function valueInContentType(): string
    {
        return match ($this) {
            self::ISO_2022_JP => 'iso-2022-jp',
            self::UTF_8 => 'utf-8',
        };
    }
    public function valueInMimeHeader(): string
    {
        return match ($this) {
            self::ISO_2022_JP => 'ISO-2022-JP',
            self::UTF_8 => 'UTF-8',
        };
    }

}
