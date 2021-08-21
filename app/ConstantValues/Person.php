<?php

namespace App\ConstantValues;

class Person
{
    public const GENDER_NOT_KNOWN = 0;
    public const GENDER_MALE      = 1;
    public const GENDER_FEMALE    = 2;
    public const GENDER_ETC       = 9;
    public const GENDERS          = [
        self::GENDER_NOT_KNOWN => '未回答',
        self::GENDER_MALE      => '男性',
        self::GENDER_FEMALE    => '女性',
        self::GENDER_ETC       => 'その他',
    ];
}
