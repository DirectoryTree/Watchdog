<?php

namespace DirectoryTree\Watchdog\Jobs\Pipes;

use Closure;
use DirectoryTree\Watchdog\LdapObject;

class AssociateConnection extends Pipe
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
        $object->ldap()->associate($this->scan->ldap);

        return $next($object);
    }
}
