<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class CarbonServiceProvider extends ServiceProvider
{
    public function register()
    {
        /* 直前の上期、下期の始まりを返す */
        Carbon::macro(
            'getFloorHalfFiscalYear',
            function () {
                /** @var Carbon $this */
                $c = $this->clone();
                if (in_array($this->month, [4, 5, 6, 7, 8, 9], true)) {
                    return $c->setDate($c->year, 4, 1)->setTime(0, 0, 0);
                }
                // 10, 11, 12, 1, 2, 3
                if (in_array($this->month, [1, 2, 3], true)) {
                    $c->subYear();
                }

                return $c->setDate($c->year, 10, 1)->setTime(0, 0, 0);
            }
        );
        /* 直前の期の始まりを返す */
        Carbon::macro(
            'getFloorFiscalYear',
            function () {
                /** @var Carbon $this */
                $c = $this->clone();
                if (in_array($this->month, [1, 2, 3], true)) {
                    $c->subYear();
                }

                return $c->setDate($c->year, 4, 1)->setTime(0, 0, 0);
            }
        );
    }
}
