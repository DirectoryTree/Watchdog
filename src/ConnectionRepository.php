<?php

namespace DirectoryTree\Watchdog;

class ConnectionRepository
{
    public static function all()
    {
        return LdapConnection::all();
    }

    /**
     * Returns the domains that should be synchronized.
     *
     * @return |\Illuminate\Database\Eloquent\Collection
     */
    public static function toSynchronize()
    {
        return static::query()->get()->filter(function (LdapConnection $domain)  {
            $frequencyInMinutes = config("watchdog.frequency", 15);

            // Get the last scan that was performed on the domain.
            $lastScan = $domain->scans()->latest()->first();

            // If no scan has taken place yet, we will
            // include this domain to be synchronized.
            if (! $lastScan) {
                return true;
            }

            // If the last scan has not yet been started, we
            // will avoid stacking scans until it has begun.
            if (! $lastScan->started_at) {
                return false;
            }

            return now()->diffInMinutes($lastScan->started_at) >= $frequencyInMinutes;
        });
    }

    /**
     * Create a new domain query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function query()
    {
        return LdapConnection::query();
    }
}
