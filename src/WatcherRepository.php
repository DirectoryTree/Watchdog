<?php

namespace DirectoryTree\Watchdog;

class WatcherRepository
{
    /**
     * Get all the LDAP watchers being monitored.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function all()
    {
        return static::query()->get();
    }

    /**
     * Get the LDAP watchers that are ready to be monitored.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function toMonitor()
    {
        return static::query()->with(['scans' => function ($query) {
            $query->latest()->limit(1);
        }])->get()->filter(function (LdapWatcher $watcher) {
            return $watcher->shouldBeScanned();
        });
    }

    /**
     * Create a new LDAP watcher query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function query()
    {
        $model = ModelRepository::get(LdapWatcher::class);

        return $model::query();
    }
}
