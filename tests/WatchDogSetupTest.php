<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\LdapConnection;
use LdapRecord\Models\ActiveDirectory\Entry;

class WatchDogSetupTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('watchdog.models', [Entry::class]);
    }

    public function test_model_connections_can_be_imported()
    {
        $this->artisan('watchdog:setup');

        $this->assertCount(1, LdapConnection::get());

        $connection = LdapConnection::first();

        $this->assertEquals('default', $connection->name);
        $this->assertEquals('default', $connection->slug);
        $this->assertEquals(Entry::class, $connection->model);
    }

    public function test_model_connections_are_not_duplicated()
    {
        $this->artisan('watchdog:setup');
        $this->artisan('watchdog:setup');

        $this->assertCount(1, LdapConnection::get());
    }
}
