<?php

namespace DirectoryTree\Watchdog\Jobs;

use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapWatcher;
use Illuminate\Foundation\Bus\Dispatchable;

class ExecuteScan
{
    use Dispatchable;

    /**
     * The LDAP connection.
     *
     * @var LdapWatcher
     */
    protected $watcher;

    /**
     * Create a new job instance.
     *
     * @param LdapWatcher $watcher
     *
     * @return void
     */
    public function __construct(LdapWatcher $watcher)
    {
        $this->watcher = $watcher;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var LdapScan $scan */
        $scan = $this->watcher->scans()->create([
            'state' => LdapScan::STATE_CREATED,
        ]);

        ImportModels::withChain([
            new ProcessImported($scan),
            new DeleteMissingObjects($scan),
            new PurgeImported($scan),
        ])->dispatch($scan);
    }
}
