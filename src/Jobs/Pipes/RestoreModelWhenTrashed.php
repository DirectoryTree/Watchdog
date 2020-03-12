<?php

namespace DirectoryTree\Watchdog\Jobs\Pipes;

use Closure;
use DirectoryTree\Watchdog\LdapObject;

class RestoreModelWhenTrashed extends Pipe
{
    /**
     * Restore the model if it's trashed.
     *
     * @param LdapObject $object
     * @param Closure    $next
     *
     * @return void
     */
    public function handle(LdapObject $object, Closure $next)
    {
        if ($object->trashed()) {
            $object->restore();
        }

        return $next($object);
    }
}
