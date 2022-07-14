<?php

namespace App\Mail\SmtpChangeableEncodingTransport;

class MailEncode
{
    public const BASE64           = 'BASE64';
    public const QUOTED_PRINTABLE = 'QUOTED_PRINTABLE';

    public static function valueInMimeHeader(string $encode): string
    {
        return match ($encode) {
            self::BASE64 => 'B',
            default      => 'Q',
        };
    }
}
