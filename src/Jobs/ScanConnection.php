<?php

namespace DirectoryTree\Watchdog\Jobs;

use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapConnection;
use Illuminate\Foundation\Bus\Dispatchable;

class ScanConnection
{
    use Dispatchable;

    /**
     * The LDAP domain.
     *
     * @var LdapConnection
     */
    protected $connection;

    /**
     * Create a new job instance.
     *
     * @param LdapConnection $domain
     *
     * @return void
     */
    public function __construct(LdapConnection $domain)
    {
        $this->connection = $domain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var LdapScan $scan */
        $scan = $this->connection->scans()->create();

        ImportConnection::withChain([
            new ProcessImported($scan),
            new DeleteMissingObjects($scan),
            new PurgeImported($scan)
        ])->dispatch($scan);
    }
}
