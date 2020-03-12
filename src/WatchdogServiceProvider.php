<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Support\ServiceProvider;
use DirectoryTree\Watchdog\Console\Commands\PingDomains;
use DirectoryTree\Watchdog\Console\Commands\Feed;
use DirectoryTree\Watchdog\Console\Commands\Setup;

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
           Feed::class,
           Setup::class,
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

        $this->loadFactoriesFrom(__DIR__.'/../database/factories');
    }
}
