<?php

namespace DirectoryTree\Watchdog\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use DirectoryTree\Watchdog\LdapChange;
use DirectoryTree\Watchdog\LdapObject;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateObjectChanges implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
            $change = new LdapChange();

            $change->object()->associate($this->object);

            $before = array_key_exists($attribute, $this->old) ? $this->old[$attribute] : [];

            // Our values will be serialized from the DetectChanges pipe. We
            // will attempt to unserialize the values, and if it values we
            // will simply save the values that cannot be unserialized.
            $after = rescue(function () use ($values) {
                return unserialize($values);
            }, $values);

            $change->fill([
                'ldap_updated_at' => $this->when,
                'attribute'       => $attribute,
                'before'          => $before,
                'after'           => $after,
            ])->save();
        }
    }
}
