<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        Validator::extendImplicit('password', function ($attribute, $value, $parameters, $validator) {
            return false;
        });
    }
}
