<?php

namespace App\Mail\SmtpChangeableEncodingTransport;

enum MailEncode
{
    case BASE64;
    case QUOTED_PRINTABLE;

    public function valueInMimeHeader(): string
    {
        return match ($this) {
            self::BASE64 => 'B',
            self::QUOTED_PRINTABLE => 'Q',
        };
    }
}
