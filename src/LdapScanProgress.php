<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Database\Eloquent\Model;

class LdapScanProgress extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ldap_scan_progress';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Set the scan progress step on creation.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $progress) {
            $step = (int) $progress->newQuery()
                ->where('scan_id', '=', $progress->scan_id)
                ->max('step');

            $progress->step = ++$step;
        });
    }

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
