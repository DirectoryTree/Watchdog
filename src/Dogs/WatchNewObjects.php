<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Conditions\IsNewObject;
use DirectoryTree\Watchdog\Notifications\ObjectCreated;

class WatchNewObjects extends Watchdog
{
    /**
     * The conditions of the watchdog.
     *
     * @var array
     */
    protected $conditions = [IsNewObject::class];

    /**
     * Get the notification for the watchdog.
     *
     * @return string
     */
    public function notification()
    {
        return ObjectCreated::class;
    }
}
