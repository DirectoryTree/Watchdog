<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Database\Eloquent\Model;

class LdapScanEntry extends Model
{
    use IsSelfReferencing;

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
        'values'    => 'array',
        'processed' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['ldap_updated_at'];

    /**
     * The belongsTo scan relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function scan()
    {
        return $this->belongsTo(ModelRepository::get(LdapScan::class), 'scan_id');
    }
}
