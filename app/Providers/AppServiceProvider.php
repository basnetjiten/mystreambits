<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Configuration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Validator::extend('mp3_extension', function($attribute, $value, $parameters, $validator) {
            return (!empty($value->getClientOriginalExtension()) && ($value->getClientOriginalExtension() == 'mp3'));
        });

        // Configurations
        if (php_sapi_name() !== 'cli')
            Configuration::reload();
    }
}
