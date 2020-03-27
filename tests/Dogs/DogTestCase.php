<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use LdapRecord\Laravel\Testing\DirectoryEmulator;

class DogTestCase extends TestCase
{
    use WithFaker;

    protected $model;

    protected $watchdogs = [];

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ldap.connections', [
            'default' => [
                'base_dn' => 'dc=local,dc=com',
            ],
        ]);

        $app['config']->set('watchdog.frequency', 0);
    }

    protected function setUp(): void
    {
        parent::setUp();

        DirectoryEmulator::setup();

        config(["watchdog.watch.{$this->model}" => (array) $this->watchdogs]);

        $this->artisan('watchdog:setup');
    }
}
