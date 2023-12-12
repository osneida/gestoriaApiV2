<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Validator::extend('unique_period_company', function ($attribute, $value, $parameters, $validator) {
            $count = DB::table('payroll_periods')->where('period', request("period"))
                ->where('company_id', (int) request("company_id"))
                ->count();
            return $count === 0;
        }, "Aquest període per aquest empresa ja està processat");
    }
}
