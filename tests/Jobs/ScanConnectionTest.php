<?php

namespace DirectoryTree\Watchdog\Tests\Jobs;

use LdapRecord\Models\Entry;
use DirectoryTree\Watchdog\LdapConnection;
use DirectoryTree\Watchdog\Jobs\ScanConnection;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use DirectoryTree\Watchdog\Tests\TestCase;

class ScanConnectionTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ldap.connections.default', [
            'base_dn' => 'dc=local,dc=com',
        ]);

        $app['config']->set('watchdog.models', [Entry::class]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('watchdog:setup');

        DirectoryEmulator::setup();
    }

    public function test_scan_records_are_created_for_connections()
    {
        $connection = LdapConnection::first();

        ScanConnection::dispatch($connection);

        $scan = $connection->scans()->first();
        $this->assertTrue($scan->success);
        $this->assertEquals(0, $scan->synchronized);
    }
}
