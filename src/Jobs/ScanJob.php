<?php

namespace DirectoryTree\Watchdog\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DirectoryTree\Watchdog\LdapScan;

class ScanJob implements ShouldQueue
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
     * Constructor.
     *
     * @param LdapScan $scan
     */
    public function __construct(LdapScan $scan)
    {
        $this->scan = $scan;
    }

    /**
     * The job failed to process.
     *
     * @param Exception $ex
     *
     * @return void
     */
    public function failed(Exception $ex)
    {
        $this->scan->fill([
            'failed'       => true,
            'message'      => $ex->getMessage(),
            'completed_at' => now(),
        ])->save();
    }
}
