<?php

namespace DirectoryTree\Watchdog\Tests;

use LdapRecord\Laravel\LdapServiceProvider;
use DirectoryTree\Watchdog\ScoutServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected function getPackageProviders($app)
    {
        return [
            ScoutServiceProvider::class,
            LdapServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ldap.logging', false);
    }
}
