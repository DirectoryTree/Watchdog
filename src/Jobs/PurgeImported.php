<?php

namespace DirectoryTree\Watchdog\Jobs;

use Illuminate\Bus\Queueable;
use DirectoryTree\Watchdog\LdapScan;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PurgeImported implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The LDAP scan to purge records upon.
     *
     * @var LdapScan
     */
    protected $scan;

    /**
     * Create a new job instance.
     *
     * @param LdapScan $scan
     */
    public function __construct(LdapScan $scan)
    {
        $this->scan = $scan;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->scan->entries()->delete();
    }
}
