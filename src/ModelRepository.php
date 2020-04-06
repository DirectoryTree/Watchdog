<?php

namespace DirectoryTree\Watchdog;

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
     * @return void
     */
    public static function swap($model, $for)
    {
        static::$models[$model] = $for;
    }
}
