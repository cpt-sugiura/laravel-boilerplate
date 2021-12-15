<?php

namespace App\Console\Commands\HealthNotify;

use App\Console\BaseCommand;

class DiskFree extends BaseCommand
{
    protected $name = 'health-notify:df';

    private const KB = 1024;
    private const MB = 1024 * 1024;
    private const GB = 1024 * 1024 * 1024;

    public function handle(): int
    {
        $dfByte   = disk_free_space('/');
        $dtByte   = disk_total_space('/');
        $usedByte = $dtByte - $dfByte;
        if ($dfByte !== false && $dfByte < 1 * self::GB) {
            dev_slack_log()->warning(implode("\n", [
                'ディスク残り容量について警告。',
                '使用率：'.number_format($usedByte / self::GB, 3)
                .' / '
                .number_format($dtByte / self::GB, 3)
                .' (GB)('.(int) ($usedByte / $dtByte * 100).'%)'
            ]));
        }

        return static::SUCCESS;
    }
}
