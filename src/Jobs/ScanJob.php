<?php

namespace DirectoryTree\Watchdog\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use DirectoryTree\Watchdog\LdapScan;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ScanJob implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * The LDAP scan to process.
     *
     * @var LdapScan
     */
    protected $scan;

    /**
     * Constructor.
     *
     * @param LdapScan $scan
     */
    public function __construct(LdapScan $scan)
    {
        $this->scan = $scan;
    }

    /**
     * Handle the failing scan job.
     *
     * @param Exception $ex
     *
     * @return void
     */
    public function failed(Exception $ex)
    {
        $this->scan->fill([
            'failed'  => true,
            'message' => $ex->getMessage(),
        ])->save();

        // If the scan has failed, we will be sure to clear out
        // any entries that were imported to prevent the table
        // from filling up inadvertently and delete the job.
        $this->scan->entries()->delete();

        $this->delete();
    }
}
