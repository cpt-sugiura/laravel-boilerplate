<?php

namespace Tests\Unit;

use Illuminate\Support\Carbon;
use Tests\TestCase;

class CarbonTest extends TestCase
{
    /**
     * 直近の上期下期の始まり取得テスト
     * @return void
     */
    public function testHalfFloorTest(): void
    {
        // 下期になるパターン
        $expect = '2018-10-01 00:00:00';
        self::assertEquals($expect, (new Carbon('2018-11-14'))->getFloorHalfFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2018-12-31'))->getFloorHalfFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2019-03-31'))->getFloorHalfFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2019-01-01'))->getFloorHalfFiscalYear()->format('Y-m-d H:i:s'));

        // 上期になるパターン
        $expect = '2019-04-01 00:00:00';
        self::assertEquals($expect, (new Carbon('2019-09-30'))->getFloorHalfFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2019-04-30'))->getFloorHalfFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2019-08-31'))->getFloorHalfFiscalYear()->format('Y-m-d H:i:s'));
    }

    /**
     * 直近の期の始まり取得テスト
     * @return void
     */
    public function testFiscalYearFloorTest(): void
    {
        $expect = '2018-04-01 00:00:00';
        self::assertEquals($expect, (new Carbon('2018-11-14'))->getFloorFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2018-12-31'))->getFloorFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2019-03-31'))->getFloorFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2019-01-01'))->getFloorFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2019-02-27'))->getFloorFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2018-04-30'))->getFloorFiscalYear()->format('Y-m-d H:i:s'));
        self::assertEquals($expect, (new Carbon('2018-04-01'))->getFloorFiscalYear()->format('Y-m-d H:i:s'));
    }
}
