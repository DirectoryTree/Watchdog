<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DirectoryTree\Watchdog\Ldap\TypeGuesser;

class LdapObject extends Model
{
    use SoftDeletes, IsSelfReferencing;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dn',
        'guid',
        'domain',
        'values',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['values' => 'array'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::updated(function (LdapObject $object) {
            $watchdogs = config('watchdog.watchdogs', []);

            app(Kennel::class)
                ->setWatchers($watchdogs)
                ->inspect($object);
        });

        // We don't need to worry about eloquent events firing
        // for change records. We'll bulk delete the changes
        // if the LDAP object is being force deleted.
        static::deleting(function(LdapObject $object) {
            if ($object->isForceDeleting()) {
                $object->changes()->delete();
            }
        });
    }

    /**
     * The belongsTo LDAP connection relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ldap()
    {
        return $this->belongsTo(LdapConnection::class, 'connection_id');
    }

    /**
     * The hasMany changes relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function changes()
    {
        return $this->hasMany(LdapChange::class, 'object_id');
    }

    /**
     * Determine if the object can have groups.
     *
     * @return bool
     */
    public function canHaveGroups()
    {
        return in_array($this->type, [
            TypeGuesser::TYPE_GROUP,
            TypeGuesser::TYPE_USER
        ]);
    }

    /**
     * Get the LDAP objects original values.
     *
     * @return array
     */
    public function getOriginalValues()
    {
        return json_decode($this->getOriginal('values'), true) ?? [];
    }

    /**
     * Get the LDAP objects updated values.
     *
     * @return array
     */
    public function getUpdatedValues()
    {
        return $this->getAttribute('values');
    }
}
