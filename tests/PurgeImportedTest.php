<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\LdapScanEntry;
use DirectoryTree\Watchdog\Jobs\PurgeImported;

class PurgeImportedTest extends TestCase
{
    public function test_job_deletes_all_scan_entries()
    {
        $entries = factory(LdapScanEntry::class)->times(10)->create();

        $this->assertCount(10, $entries);

        $scan = $entries->first()->scan;

        PurgeImported::dispatch($scan);

        $this->assertCount(0, $scan->entries()->get());
    }
}
