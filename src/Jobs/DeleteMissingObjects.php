<?php

namespace DirectoryTree\Watchdog\Jobs;

use Illuminate\Bus\Queueable;
use DirectoryTree\Watchdog\LdapScan;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteMissingObjects implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The LDAP scan to process.
     *
     * @var LdapScan
     */
    protected $scan;

    /**
     * Create a new job instance.
     *
     * @param LdapScan $scan
     *
     * @return void
     */
    public function __construct(LdapScan $scan)
    {
        $this->scan = $scan;
    }

    /**
     * Soft-delete all LDAP objects that were missing from the scan.
     *
     * @return void
     */
    public function handle()
    {
        $guids = $this->scan->entries()->pluck('guid');

        $this->scan->ldap->objects()
            ->whereNotIn('guid', $guids)
            ->delete();
    }
}
