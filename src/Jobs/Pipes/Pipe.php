<?php

namespace DirectoryTree\Watchdog\Jobs\Pipes;

use Closure;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapScanEntry;

abstract class Pipe
{
    /**
     * The LDAP scan.
     *
     * @var LdapScan
     */
    protected $scan;

    /**
     * The LDAP scan entry.
     *
     * @var LdapScanEntry
     */
    protected $entry;

    /**
     * Constructor.
     *
     * @param LdapScan $scan
     * @param LdapScanEntry $entry
     */
    public function __construct(LdapScan $scan, LdapScanEntry $entry)
    {
        $this->scan = $scan;
        $this->entry = $entry;
    }

    /**
     * Perform operations on the LDAP object being synchronized.
     *
     * @param LdapObject $object
     * @param Closure    $next
     *
     * @return void
     */
    abstract public function handle(LdapObject $object, Closure $next);
}
