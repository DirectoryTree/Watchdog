<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\LdapWatcher;
use LdapRecord\Models\ActiveDirectory\Entry;

class WatchDogSetupTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $model = Entry::class;

        $app['config']->set("watchdog.watch.{$model}", []);
    }

    public function test_model_connections_can_be_imported()
    {
        $this->artisan('watchdog:setup');

        $this->assertCount(1, LdapWatcher::get());

        $connection = LdapWatcher::first();

        $this->assertEquals('Default', $connection->name);
        $this->assertEquals(Entry::class, $connection->model);
    }

    public function test_model_connections_are_not_duplicated()
    {
        $this->artisan('watchdog:setup');
        $this->artisan('watchdog:setup');

        $this->assertCount(1, LdapWatcher::get());
    }
}
