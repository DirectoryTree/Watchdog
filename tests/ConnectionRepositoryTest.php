<?php

namespace DirectoryTree\Watchdog\Tests;

use LdapRecord\Models\Entry;
use DirectoryTree\Watchdog\LdapWatcher;
use DirectoryTree\Watchdog\ConnectionRepository;

class ConnectionRepositoryTest extends TestCase
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

    public function test_to_synchronize_returns_models_to_synchronize_with_proper_frequency()
    {
        $this->assertCount(1, ConnectionRepository::toMonitor());

        $connection = LdapWatcher::first();
        // Default is 15 minutes.
        $connection->scans()->create(['started_at' => now()->subMinutes(15)]);

        $toSync = ConnectionRepository::toMonitor();
        $this->assertCount(1, $toSync);
        $this->assertTrue($connection->is($toSync->first()));

        config(['watchdog.frequency' => 20]);
        $this->assertCount(0, ConnectionRepository::toMonitor());
    }
}
