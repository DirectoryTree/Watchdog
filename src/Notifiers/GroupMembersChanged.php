<?php

namespace DirectoryTree\Watchdog\Notifiers;

use DirectoryTree\Watchdog\Notifiers\Conditions\MembersChanged;

class GroupMembersChanged extends Notifier
{
    protected $conditions = [
        MembersChanged::class,
    ];

    /**
     * @inheritDoc
     */
    public function name()
    {
        return 'Group Members Changed';
    }

    /**
     * @inheritDoc
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function notify()
    {
        // TODO: Implement notify() method.
    }
}
