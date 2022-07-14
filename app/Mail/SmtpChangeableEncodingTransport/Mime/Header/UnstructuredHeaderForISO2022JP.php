<?php

namespace App\Mail\SmtpChangeableEncodingTransport\Mime\Header;

use Symfony\Component\Mime\Header\UnstructuredHeader;

class UnstructuredHeaderForISO2022JP extends UnstructuredHeader
{
    public function getBodyAsString(): string
    {
        return mb_encode_mimeheader($this->getValue(), 'ISO-2022-JP', 'Q');
    }
}
