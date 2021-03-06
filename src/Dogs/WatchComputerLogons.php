<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Ldap\TypeResolver;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\NewLogin;
use DirectoryTree\Watchdog\Notifications\ComputerLoginHasOccurred;

class WatchComputerLogons extends Watchdog
{
    /**
     * The conditions of the watchdog.
     *
     * @var array
     */
    protected $conditions = [NewLogin::class];

    /**
     * {@inheritdoc}
     */
    public function bark()
    {
        if ($this->object->type == TypeResolver::TYPE_COMPUTER) {
            parent::bark();
        }
    }

    /**
     * Get the notification for the watchdog.
     *
     * @return string
     */
    public function notification()
    {
        return ComputerLoginHasOccurred::class;
    }
}
