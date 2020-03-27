<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\Commands\Setup;
use DirectoryTree\Watchdog\Commands\Monitor;
use DirectoryTree\Watchdog\Commands\MakeWatchdog;
use DirectoryTree\Watchdog\WatchdogServiceProvider;

class WatchdogServiceProviderTest extends TestCase
{
    public function test_migrations_are_loaded()
    {
        $migrations = [
            \CreateLdapChangesTable::class,
            \CreateLdapWatchersTable::class,
            \CreateLdapObjectsTable::class,
            \CreateLdapScansTable::class,
            \CreateLdapScanEntriesTable::class,
        ];

        $this->assertCount(5, array_filter($migrations, function ($migration) {
            return class_exists($migration);
        }));
    }

    public function test_config_is_publishable()
    {
        $this->artisan('vendor:publish', ['--provider' => WatchdogServiceProvider::class, '--no-interaction' => true]);

        $this->assertFileExists(config_path('watchdog.php'));
    }

    public function test_commands_are_resolved()
    {
        $this->assertTrue($this->app->resolved(Monitor::class));
        $this->assertTrue($this->app->resolved(Setup::class));
        $this->assertTrue($this->app->resolved(MakeWatchdog::class));
    }
}
