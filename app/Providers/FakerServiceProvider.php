<?php

namespace App\Providers;

use App\Library\Faker\Providers\Address;
use App\Library\Faker\Providers\Image;
use Faker\Factory;
use Faker\Generator as Faker;
use Illuminate\Support\ServiceProvider;

class FakerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Faker::class, static function () {
            $fakerLocale = app()->get('config')->get('app.faker_locale', 'ja_JP');
            $faker = Factory::create($fakerLocale);
            $faker->addProvider(new Address($faker));
            $faker->addProvider(new Image($faker));

            return $faker;
        });
    }
}
