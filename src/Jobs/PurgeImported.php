<?php

namespace DirectoryTree\Watchdog\Jobs;

use DirectoryTree\Watchdog\LdapScan;

class PurgeImported extends ScanJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->scan->update(['state' => LdapScan::STATE_PURGING]);

        $this->scan->entries()->delete();

        $this->scan->update([
            'completed_at' => now(),
            'state'        => LdapScan::STATE_PURGED,
        ]);
    }
}
