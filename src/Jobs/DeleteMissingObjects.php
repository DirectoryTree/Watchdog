<?php

namespace DirectoryTree\Watchdog\Jobs;

use DirectoryTree\Watchdog\LdapScan;

class DeleteMissingObjects extends ScanJob
{
    /**
     * Soft-delete all LDAP objects that were missing from the scan.
     *
     * @return void
     */
    public function handle()
    {
        $this->scan->progress()->create(['state' => LdapScan::STATE_DELETING_MISSING]);

        $guids = $this->scan->entries()->pluck('guid');

        $this->scan->watcher->objects()
            ->whereNotIn('guid', $guids)
            ->delete();

        $this->scan->progress()->create(['state' => LdapScan::STATE_DELETED_MISSING]);
    }
}
