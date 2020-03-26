<?php

namespace DirectoryTree\Watchdog\Tests;

use LdapRecord\Laravel\LdapServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use DirectoryTree\Watchdog\WatchdogServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected function getPackageProviders($app)
    {
        return [
            WatchdogServiceProvider::class,
            LdapServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $config = $app['config'];

        $config->set('database.default', 'testbench');
        $config->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);

        $config->set('ldap.logging', false);
    }
}
