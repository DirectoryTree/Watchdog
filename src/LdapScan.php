<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Database\Eloquent\Model;

class LdapScan extends Model
{
    /**
     * The various LDAP scan states.
     */
    const STATE_CREATED = 'created';
    const STATE_IMPORTING = 'importing';
    const STATE_IMPORTED = 'imported';
    const STATE_PROCESSING = 'processing';
    const STATE_PROCESSED = 'processed';
    const STATE_DELETING_MISSING = 'deleting';
    const STATE_DELETED_MISSING = 'deleted';
    const STATE_PURGING = 'purging';
    const STATE_PURGED = 'purged';

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
    protected $casts = ['failed' => 'bool'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (self $scan) {
            $scan->entries()->delete();
        });
    }

    /**
     * The belongsTo watcher relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function watcher()
    {
        return $this->belongsTo(LdapWatcher::class, 'watcher_id');
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
     * Determine whether the scan was successful.
     *
     * @return bool
     */
    public function getSuccessfulAttribute()
    {
        return $this->started_at && $this->completed_at && $this->state == LdapScan::STATE_PURGED;
    }

    /**
     * Determine whether the scan is running.
     *
     * @return bool
     */
    public function getRunningAttribute()
    {
        return !in_array($this->state, [LdapScan::STATE_CREATED, LdapScan::STATE_PURGED]);
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
