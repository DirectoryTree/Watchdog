<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Ldap\TypeResolver;
use DirectoryTree\Watchdog\Notifications\PasswordHasChanged;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\PasswordChanged;

class WatchPasswordChanges extends Watchdog
{
    /**
     * The watchdog conditions.
     *
     * @var array
     */
    protected $conditions = [PasswordChanged::class];

    /**
     * {@inheritdoc}
     */
    public function bark()
    {
        // Here we will make sure to only send a notification on the event that the
        // object is a user and not a computer. Computer passwords are updated
        // automatically and often, and this will throw off our statistics.
        if ($this->object->type == TypeResolver::TYPE_USER) {
            parent::bark();
        }
    }

    /**
     * The watchdog notification.
     *
     * @return string
     */
    public function notification()
    {
        return PasswordHasChanged::class;
    }
}
