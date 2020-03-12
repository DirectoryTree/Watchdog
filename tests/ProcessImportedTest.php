<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapScanEntry;
use DirectoryTree\Watchdog\LdapConnection;
use DirectoryTree\Watchdog\Jobs\ProcessImported;

class ProcessImportedTest extends TestCase
{
    public function test_scan_entries_are_imported()
    {
        $scan = factory(LdapScan::class)->create();

        factory(LdapScanEntry::class)->times(10)->create([
            'scan_id' => $scan->id,
        ]);

        ProcessImported::dispatch($scan);

        $this->assertCount(10, LdapObject::get());
    }

    public function test_trashed_objects_are_restored_when_scanned()
    {
        $ldap = factory(LdapConnection::class)->create();

        $scan = factory(LdapScan::class)->create([
            'connection_id' => $ldap->id,
        ]);

        $object = factory(LdapObject::class)->create([
            'connection_id' => $ldap->id,
            'deleted_at' => now(),
        ]);

        factory(LdapScanEntry::class)->create([
            'scan_id' => $scan->id,
            'guid' => $object->guid,
        ]);

        $this->assertTrue($object->trashed());

        ProcessImported::dispatch($scan);

        $this->assertFalse($object->fresh()->trashed());
        $this->assertEquals(1, LdapObject::count());
    }
}
