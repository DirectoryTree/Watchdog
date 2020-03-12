<?php

namespace DirectoryTree\Watchdog\Jobs\Pipes;

use Closure;
use DirectoryTree\Watchdog\LdapObject;

class HydrateProperties extends Pipe
{
    /**
     * Hydrate the object with the scanned entry properties.
     *
     * @param LdapObject $object
     * @param Closure    $next
     *
     * @return void
     */
    public function handle(LdapObject $object, Closure $next)
    {
        $object->dn = $this->entry->dn;
        $object->name = $this->entry->name;
        $object->type = $this->entry->type;
        $object->values = $this->entry->values;

        return $next($object);
    }
}
