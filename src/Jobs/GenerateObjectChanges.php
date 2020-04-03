<?php

namespace DirectoryTree\Watchdog\Jobs;

use Carbon\Carbon;
use DirectoryTree\Watchdog\LdapObject;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateObjectChanges
{
    use Dispatchable;
    /**
     * The LDAP object that has been modified.
     *
     * @var LdapObject
     */
    protected $object;

    /**
     * When the LDAP object was modified.
     *
     * @var Carbon
     */
    protected $when;

    /**
     * The LDAP objects modified attributes and their new values.
     *
     * @var array
     */
    protected $modified = [];

    /**
     * The LDAP objects old attributes.
     *
     * @var array
     */
    protected $old = [];

    /**
     * Create a new job instance.
     *
     * @param LdapObject $object
     * @param Carbon     $when
     * @param array      $modified
     * @param array      $old
     *
     * @return void
     */
    public function __construct(LdapObject $object, Carbon $when, array $modified = [], array $old = [])
    {
        $this->object = $object;
        $this->when = $when;
        $this->modified = $modified;
        $this->old = $old;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->modified as $attribute => $values) {
            $before = array_key_exists($attribute, $this->old) ? $this->old[$attribute] : [];

            // Our values will be serialized from the DetectChanges pipe. We
            // will attempt to unserialize the values, and if it fails we
            // will simply save the values that cannot be unserialized.
            $after = rescue(function () use ($values) {
                return unserialize($values);
            }, $values);

            $this->object->changes()->create([
                'ldap_updated_at' => $this->when,
                'attribute'       => $attribute,
                'before'          => $before,
                'after'           => $after,
            ]);
        }
    }
}
