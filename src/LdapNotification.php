<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Database\Eloquent\Model;

class LdapNotification extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data'     => 'array',
        'channels' => 'array',
    ];

    /**
     * The belongsTo LDAP object relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function object()
    {
        return $this->belongsTo(LdapObject::class, 'object_id');
    }
}
