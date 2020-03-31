<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\LdapWatcher;
use LdapRecord\Models\ActiveDirectory\Entry;

class WatchdogSetupTest extends TestCase
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

        $watcher = LdapWatcher::first();

        $this->assertEquals('Default', $watcher->name);
        $this->assertEquals(Entry::class, $watcher->model);
    }

    public function test_model_connections_are_not_duplicated()
    {
        $this->artisan('watchdog:setup');
        $this->artisan('watchdog:setup');

        $this->assertCount(1, LdapWatcher::get());
    }
}
