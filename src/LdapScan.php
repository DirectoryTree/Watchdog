<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Database\Eloquent\Model;

class LdapScan extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'started_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['success' => 'boolean'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function(LdapScan $scan) {
            $scan->entries()->delete();
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
     * The hasMany entries relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany(LdapScanEntry::class, 'scan_id');
    }

    /**
     * Begin querying root scan entries.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function rootEntries()
    {
        return $this->entries()->roots();
    }

    /**
     * Begin querying successful scans.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', '=', true);
    }

    /**
     * Determine whether the scan is running.
     *
     * @return bool
     */
    public function getRunningAttribute()
    {
        return !is_null($this->started_at) && is_null($this->completed_at);
    }

    /**
     * Get the duration of the scan.
     *
     * @return string|null
     */
    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->longAbsoluteDiffForHumans($this->completed_at);
        }
    }
}
