<?php

namespace App\ConstantValues;

class DateTime
{
    public const SQL_DAY_OF_WEEK_SUNDAY    = 1;
    public const SQL_DAY_OF_WEEK_MONDAY    = 2;
    public const SQL_DAY_OF_WEEK_TUESDAY   = 3;
    public const SQL_DAY_OF_WEEK_WEDNESDAY = 4;
    public const SQL_DAY_OF_WEEK_THURSDAY  = 5;
    public const SQL_DAY_OF_WEEK_FRIDAY    = 6;
    public const SQL_DAY_OF_WEEK_SATURDAY  = 7;
    public const SQL_WORKDAYS              = [2, 3, 4, 5, 6];
    public const SQL_HOLIDAYS              = [1, 7];
    public const PHP_DAY_OF_WEEK2JA        = [
        0 => '日',
        1 => '月',
        2 => '火',
        3 => '水',
        4 => '木',
        5 => '金',
        6 => '土',
    ];
}
