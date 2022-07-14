<?php

namespace App\Mail\SmtpChangeableEncodingTransport;

class MailCharset
{
    public const ISO_2022_JP = 'ISO_2022_JP';
    public const UTF_8       = 'UTF_8';

    public static function valueInContentType(string $charset): string
    {
        return match ($charset) {
            self::ISO_2022_JP => 'iso-2022-jp',
            default           => 'utf-8',
        };
    }

    public static function valueInMimeHeader(string $charset): string
    {
        return match ($charset) {
            self::ISO_2022_JP => 'ISO-2022-JP',
            default           => 'UTF-8',
        };
    }
}
