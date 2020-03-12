<?php

namespace DirectoryTree\Watchdog\Jobs\Pipes;

use Closure;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\Jobs\GenerateObjectChanges;

class DetectChanges extends Pipe
{
    /**
     * Perform operations on the LDAP object model being synchronized.
     *
     * @param LdapObject $object
     * @param Closure    $next
     *
     * @return void
     */
    public function handle(LdapObject $object, Closure $next)
    {
        $newAttributes = $this->entry->values ?? [];
        $oldAttributes = $object->values ?? [];

        // Determine any differences from our last sync.
        $modifications = array_diff(
            array_map('serialize', $newAttributes),
            array_map('serialize', $oldAttributes)
        );

        // We don't want to create changes for newly imported objects.
        if ($object->exists && count($modifications) > 0) {
            $when = $this->entry->ldap_updated_at;

            GenerateObjectChanges::dispatch($object, $when, $modifications, $oldAttributes);
        }

        return $next($object);
    }
}
