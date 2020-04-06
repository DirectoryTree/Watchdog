<?php

namespace DirectoryTree\Watchdog;

class WatcherRepository
{
    /**
     * Get all the LDAP connections being monitored.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function all()
    {
        return static::query()->get();
    }

    /**
     * Get the LDAP connections that are ready to be monitored.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function toMonitor()
    {
        return static::query()->get()->filter(function (LdapWatcher $watcher) {
            $frequencyInMinutes = config('watchdog.frequency', 5);

            $lastScan = $watcher->scans()->latest()->first();

            // If no scan exists, we'll initiate one now.
            if (!$lastScan) {
                return true;
            }

            // If the last scan has not yet been started, we
            // will avoid stacking scans until it has begun.
            return is_null($lastScan->started_at)
                ? false
                : now()->diffInMinutes($lastScan->started_at) >= $frequencyInMinutes;
        });
    }

    /**
     * Create a new LDAP connection query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function query()
    {
        $model = ModelRepository::get(LdapWatcher::class);

        return $model::query();
    }
}
