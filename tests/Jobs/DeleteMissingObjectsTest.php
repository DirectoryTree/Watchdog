<?php

namespace DirectoryTree\Watchdog\Tests\Jobs;

use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapScanEntry;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Jobs\DeleteMissingObjects;

class DeleteMissingObjectsTest extends TestCase
{
    public function test_objects_missing_from_scan_are_soft_deleted()
    {
        $scan = factory(LdapScan::class)->create();

        $entries = factory(LdapScanEntry::class)->times(10)->create([
            'scan_id' => $scan->id,
        ]);

        $this->assertCount(10, $entries);

        $object = factory(LdapObject::class)->create([
            'connection_id' => $scan->ldap->id,
        ]);

        $this->assertTrue($object->exists);

        DeleteMissingObjects::dispatch($scan);

        $this->assertTrue($object->fresh()->trashed());
    }
}
