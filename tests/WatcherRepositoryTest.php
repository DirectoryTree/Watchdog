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
        config(['watchdog.frequency' => 0]);
        $this->assertCount(1, WatcherRepository::toMonitor());

        $watcher = LdapWatcher::first();

        factory(LdapScan::class)->create([
            'watcher_id' => $watcher->id,
            'started_at' => now()->subMinutes(5),
        ]);

        config(['watchdog.frequency' => 6]);
        $this->assertCount(0, WatcherRepository::toMonitor());
    }

    public function test_to_monitor_will_not_return_models_to_synchronize_when_last_scan_has_not_completed()
    {
        config(['watchdog.frequency' => 5]);

        $watcher = LdapWatcher::first();

        $scan = factory(LdapScan::class)->create([
            'watcher_id' => $watcher->id,
            'started_at' => now()->subMinutes(5),
        ]);

        $this->assertCount(0, WatcherRepository::toMonitor());

        $scan->fill(['completed_at' => now()])->save();

        $this->assertCount(1, WatcherRepository::toMonitor());
    }

    public function test_to_monitor_will_not_return_models_to_synchronize_when_scan_is_in_progress()
    {
        config(['watchdog.frequency' => 0]);
        $watcher = LdapWatcher::first();

        $scan = factory(LdapScan::class)->create([
            'watcher_id' => $watcher->id,
        ]);

        $scan->progress()->create(['state' => LdapScan::STATE_PURGING]);

        $this->assertCount(0, WatcherRepository::toMonitor());

        $scan->progress()->create(['state' => LdapScan::STATE_PURGED]);

        $this->assertCount(1, WatcherRepository::toMonitor());
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
