<?php

namespace DirectoryTree\Watchdog\Jobs\Pipes;

use Closure;
use DirectoryTree\Watchdog\LdapObject;

class AssociateWatcher extends Pipe
{
    /**
     * Perform operations on the LDAP object model.
     *
     * @param LdapObject $object
     * @param Closure    $next
     *
     * @return void
     */
    public function handle(LdapObject $object, Closure $next)
    {
        $object->watcher()->associate($this->scan->watcher);

        return $next($object);
    }
}
