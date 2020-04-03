<?php

namespace DirectoryTree\Watchdog\Tests\Jobs;

use LdapRecord\Models\Entry;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapWatcher;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Jobs\ExecuteScan;
use LdapRecord\Laravel\Testing\DirectoryEmulator;

class ExecuteScanTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ldap.connections.default', [
            'base_dn' => 'dc=local,dc=com',
        ]);

        $model = Entry::class;

        $app['config']->set("watchdog.watch.{$model}", []);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('watchdog:setup');

        DirectoryEmulator::setup();
    }

    public function test_scan_records_are_created_for_connections()
    {
        $watcher = LdapWatcher::first();

        ExecuteScan::dispatch($watcher);

        $scan = $watcher->scans()->first();
        $this->assertTrue($scan->successful);
        $this->assertEquals(LdapScan::STATE_PURGED, $scan->state);
        $this->assertEquals(0, $scan->processed);
    }
}
