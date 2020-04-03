<?php

namespace DirectoryTree\Watchdog\Tests;

use LdapRecord\Models\Entry;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapWatcher;
use DirectoryTree\Watchdog\WatcherRepository;

class WatcherRepositoryTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('watchdog.watch', [Entry::class => []]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('watchdog:setup');
    }

    public function test_to_monitor_returns_models_to_synchronize_with_proper_frequency()
    {
        $this->assertCount(1, WatcherRepository::toMonitor());

        config(['watchdog.frequency' => 5]);

        $watcher = LdapWatcher::first();

        factory(LdapScan::class)->create([
            'watcher_id' => $watcher->id,
            'started_at' => now()->subMinutes(5),
        ]);

        $toSync = WatcherRepository::toMonitor();
        $this->assertCount(1, $toSync);
        $this->assertTrue($watcher->is($toSync->first()));

        config(['watchdog.frequency' => 6]);
        $this->assertCount(0, WatcherRepository::toMonitor());
    }

    public function test_to_monitor_does_not_return_models_that_have_scans_waiting_to_be_finished()
    {
        $toSync = WatcherRepository::toMonitor();
        $this->assertCount(1, $toSync);

        $watcher = LdapWatcher::first();

        factory(LdapScan::class)->create(['watcher_id' => $watcher->id]);

        $toSync = WatcherRepository::toMonitor();
        $this->assertCount(0, $toSync);
    }
}
