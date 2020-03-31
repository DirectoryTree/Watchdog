<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LdapWatcher extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (self $watcher) {
            $watcher->scans()->each(function (LdapScan $scan) {
                $scan->delete();
            });

            // The connection may have a large amount of objects. We
            // will chunk our results to keep memory usage low
            // and so object deletion events are fired.
            $watcher->objects()->chunk(500, function ($objects) {
                /** @var LdapObject $object */
                foreach ($objects as $object) {
                    $object->forceDelete();
                }
            });
        });
    }

    /**
     * The hasMany LDAP scans relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scans()
    {
        return $this->hasMany(LdapScan::class, 'watcher_id');
    }

    /**
     * The hasMany objects relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function objects()
    {
        return $this->hasMany(LdapObject::class, 'watcher_id');
    }

    /**
     * The hasManyThrough changes relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function changes()
    {
        return $this->hasManyThrough(LdapChange::class, LdapObject::class, 'watcher_id', 'object_id');
    }
}
