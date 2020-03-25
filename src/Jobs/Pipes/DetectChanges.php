<?php

namespace DirectoryTree\Watchdog\Jobs\Pipes;

use Closure;
use Illuminate\Support\Collection;
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

        collect($modifications)->reject(function ($value, $attribute) {
            return in_array($attribute, config('watchdog.attributes.ignore', []));
        })->when($object->exists, function (Collection $modifications) use ($object, $oldAttributes) {
            $when = $this->entry->ldap_updated_at;

            GenerateObjectChanges::dispatch($object, $when, $modifications->toArray(), $oldAttributes);
        });

        return $next($object);
    }
}
