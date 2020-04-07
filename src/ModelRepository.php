<?php

namespace DirectoryTree\Watchdog;

use Exception;

class ModelRepository
{
    /**
     * The watchdog models.
     *
     * @var array
     */
    protected static $models = [
        LdapScan::class         => LdapScan::class,
        LdapObject::class       => LdapObject::class,
        LdapChange::class       => LdapChange::class,
        LdapWatcher::class      => LdapWatcher::class,
        LdapScanEntry::class    => LdapScanEntry::class,
        LdapScanProgress::class => LdapScanProgress::class,
        LdapNotification::class => LdapNotification::class,
    ];

    /**
     * Get all of the Watchdog Eloquent models.
     *
     * @return array
     */
    public static function all()
    {
        return static::$models;
    }

    /**
     * Get the model to use for the given model.
     *
     * @param string $model
     *
     * @return string
     */
    public static function get($model)
    {
        return static::$models[$model];
    }

    /**
     * Swap the model to use for another.
     *
     * @param string $model
     * @param string $for
     *
     * @throws Exception
     *
     * @return void
     */
    public static function swap($model, $for)
    {
        if (is_null(static::$models[$model] ?? null)) {
            throw new Exception("Model [$model] does not exist.");
        }

        static::$models[$model] = $for;
    }
}
