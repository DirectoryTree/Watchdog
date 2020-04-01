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

        $app['config']->set("watchdog.watch.$model", []);
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

    public function test_new_watcher_is_setup_for_new_model()
    {
        $this->artisan('watchdog:setup');

        $this->assertCount(1, LdapWatcher::get());

        $model = TestWatchdogModelStub::class;

        config(["watchdog.watch.$model" => []]);

        $this->artisan('watchdog:setup');

        $this->assertCount(2, LdapWatcher::get());
    }
}

class TestWatchdogModelStub extends Entry
{
    //
}
