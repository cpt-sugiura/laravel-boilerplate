<?php

namespace App\Console\Commands\ForDevelop\MakeTestData;

use Doctrine\DBAL\Schema\Column;
use Exception;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Str;

class MakeTestValue
{
    private Faker $faker;

    public function __construct()
    {
        $this->faker = app()->make(Faker::class);
    }

    /**
     * @param  Column                     $col
     * @param  Model                      $model
     * @param  ShowColumnRecordModel      $showColRes
     * @throws Exception
     * @return int|string|Point|bool|null
     */
    public function getTestValue(Column $col, Model $model, ShowColumnRecordModel $showColRes): int|string|null|Point|bool
    {
        $colName = $col->getName();
        if ($this->isAllocateNull($col)) {
            return null;
        }

        $val = $this->getValBySpacialPool($model, $col)
            ?? $this->getValForSpacialTypeColumn($showColRes);
        if (isset($val)) {
            return $val;
        }
        [$ret, $err] = $this->getValByFaker($colName);
        if (! $err) {
            return $ret;
        }

        return $this->getRandomValueByCasts($model, $colName, $col, $showColRes);
    }

    private function makeStringTestData(Column $col, ShowColumnRecordModel $showColRes): string
    {
        if (! empty($showColRes->getEnumOptions())) {
            return fast_array_random($showColRes->getEnumOptions());
        }

        $base    = ($col->getComment() ?? $col->getName()).'_'.Str::random(4);
        $trimmed = substr($base, -$col->getLength());

        return iconv('UTF-8', 'UTF-8//IGNORE', $trimmed);
    }

    /**
     * @return Point
     */
    private function getRandomPoint(): Point
    {
        return new Point(
            $this->rand(34.673, 34.743),
            $this->rand(137.667, 137.729),
        );
    }

    /**
     * @param  Model                 $model
     * @param  string                $colName
     * @param  Column                $col
     * @param  ShowColumnRecordModel $showColRes
     * @throws Exception
     * @return bool|int|string
     */
    private function getRandomValueByCasts(Model $model, string $colName, Column $col, ShowColumnRecordModel $showColRes): string|int|bool
    {
        $casts = $model->getCasts();

        return match ($casts[$colName] ?? 'undefined') {
            'string', 'undefined' => $this->makeStringTestData($col, $showColRes),
            'date', 'datetime' => date('Y-m-d H:i:s', random_int(strtotime('-1 months'), strtotime('+1 months'))),
            'integer', 'float', 'numeric' => random_int(0, 10000),
            'boolean' => (bool) random_int(0, 1),
        };
    }

    /**
     * @param  string $colName
     * @return array  [$val, $madeVal]
     */
    private function getValByFaker(string $colName): array
    {
        if (str_contains($colName, 'email')) {
            return [$this->faker->safeEmail(), false];
        }
        if ($colName === 'tel' || str_contains($colName, '_tel')) {
            return [$this->faker->phoneNumber(), false];
        }
        if ($colName === 'password' || str_contains($colName, 'password')) {
            return [\Hash::make($this->faker->password()), false];
        }
        if ($colName === 'token' || str_contains($colName, '_token')) {
            return [Str::random(32), false];
        }
        if ($colName === 'zip') {
            return [$this->faker->postcode(), false];
        }
        if ($colName === 'address') {
            return [$this->faker->prefecture().$this->faker->addressDetail(), false];
        }
        try {
            return [$this->faker->$colName, false];
        } catch (Exception $e) {
            try {
                return [$this->faker->$colName(), false];
            } catch (Exception $e) {
                return [null, true];
            }
        }
    }

    /**
     * @param  Column    $col
     * @throws Exception
     * @return bool
     */
    private function isAllocateNull(Column $col): bool
    {
        if ($col->getName() === 'created_at' || $col->getName() === 'updated_at') {
            return false;
        }
        if ($col->getName() === 'deleted_at') {
            return random_int(1, 100) >= 25;
        }

        return ! $col->getNotnull() && random_int(1, 100) <= 25;
    }

    /**
     * @param  ShowColumnRecordModel   $showColRes
     * @return false|Point|string|null
     */
    private function getValForSpacialTypeColumn(ShowColumnRecordModel $showColRes): string|null|false|Point
    {
        return match ($showColRes->type) {
            'point' => $this->getRandomPoint(),
            'json'  => json_encode(''),
            default => null,
        };
    }

    private function getValBySpacialPool(Model $model, Column $col)
    {
        $pool = config('test_data.'.$model::class.'.'.$col->getName())
            ?? config('test_data.'.$model->getTable().'.'.$col->getName());
        if (! $pool) {
            return null;
        }

        $val = fast_array_random($pool);
        if ($val instanceof \Closure) {
            return $val();
        }

        return $val;
    }

    /**
     * @param  float|int $min
     * @param  float|int $max
     * @return float
     */
    protected function rand(float|int $min = 0, float|int $max = PHP_INT_MAX): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
