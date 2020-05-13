<?php

use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapWatcher;
use DirectoryTree\Watchdog\Tests\TestCase;

class LdapScanTest extends TestCase
{
    public function test_ldap_scan_completed_attribute()
    {
        $watcher = factory(LdapWatcher::class)->create();

        $scan = factory(LdapScan::class)->create(['watcher_id' => $watcher->id]);
        $this->assertFalse($scan->completed);

        $scan = factory(LdapScan::class)->create([
            'watcher_id' => $watcher->id,
            'completed_at' => now(),
        ]);
        $this->assertTrue($scan->completed);

        $scan = factory(LdapScan::class)->create([
            'watcher_id' => $watcher->id,
            'failed' => true,
        ]);
        $this->assertTrue($scan->failed);

        $scan = factory(LdapScan::class)->create(['watcher_id' => $watcher->id]);
        $scan->progress()->create(['state' => LdapScan::STATE_IMPORTING]);
        $this->assertFalse($scan->completed);

        $scan = factory(LdapScan::class)->create(['watcher_id' => $watcher->id]);
        $scan->progress()->create(['state' => LdapScan::STATE_PURGED]);
        $this->assertTrue($scan->completed);
    }
}
