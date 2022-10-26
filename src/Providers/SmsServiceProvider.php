<?php

namespace SaeidSharafi\LaravelSms\Providers;

use Illuminate\Support\ServiceProvider;
use SaeidSharafi\LaravelSms\Sms;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/sms.php', 'sms'
        );

        $this->app->bind('Sms', function(){
            return new Sms();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->publishes([
            __DIR__.'/../Config/sms.php' => config_path('sms.php'),
        ]);

    }
}
