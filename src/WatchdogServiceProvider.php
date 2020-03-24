<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Support\ServiceProvider;
use DirectoryTree\Watchdog\Commands\Monitor;
use DirectoryTree\Watchdog\Commands\Setup;
use DirectoryTree\Watchdog\Commands\MakeWatchdog;

class WatchdogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
       $this->commands([
           Setup::class,
           Monitor::class,
           MakeWatchdog::class,
       ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            $this->publishes([
                __DIR__.'/../config/watchdog.php' => config_path('watchdog.php'),
            ], 'config');
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'watchdog');

        $this->loadFactoriesFrom(__DIR__.'/../database/factories');
    }
}
