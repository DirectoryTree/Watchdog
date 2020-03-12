<?php

namespace DirectoryTree\Watchdog\Jobs\Pipes;

use Closure;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapScanEntry;

class AssociateParent extends Pipe
{
    /**
     * Associate the parent model if it exists, or detach it if not.
     *
     * @param LdapObject $object
     * @param Closure    $next
     *
     * @return void
     */
    public function handle(LdapObject $object, Closure $next)
    {
        if ($this->entry->isChild() && $parentEntry = $this->findParentScanEntry()) {
            $object->parent()->associate($this->findParentObject($parentEntry));
        }

        return $next($object);
    }

    /**
     * Find the parent object.
     *
     * @param LdapScanEntry $parent
     *
     * @return LdapObject|null
     */
    protected function findParentObject(LdapScanEntry $parent)
    {
        return LdapObject::withTrashed()->where('guid', '=', $parent->guid)->first();
    }

    /**
     * Find the parent scan entry.
     *
     * @return LdapScanEntry|null
     */
    protected function findParentScanEntry()
    {
        return LdapScanEntry::find($this->entry->parent_id);
    }
}
