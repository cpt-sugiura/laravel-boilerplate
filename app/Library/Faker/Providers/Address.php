<?php

namespace App\Library\Faker\Providers;

class Address extends \Faker\Provider\ja_JP\Address
{
    /**
     * @var array 市区町村以下の地名
     */
    protected static array $addressDetailFormats = [
        '{{city}}{{ward}}{{streetAddress}}',
        '{{city}}{{ward}}{{streetAddress}} {{secondaryAddress}}',
    ];

    /**
     * 市区町村以下の地名
     * @return string
     */
    public function addressDetail(): string
    {
        $format = static::randomElement(static::$addressDetailFormats);

        return $this->generator->parse($format);
    }
}
