<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\Drivers\Web\WebDriver;

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
        DriverManager::loadDriver(TelegramDriver::class);
        DriverManager::loadDriver(WebDriver::class);

    }
}
