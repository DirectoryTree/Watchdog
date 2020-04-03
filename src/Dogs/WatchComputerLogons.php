<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Ldap\TypeGuesser;
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
     * {@inheritDoc}
     */
    public function bark()
    {
        if ($this->object->type == TypeGuesser::TYPE_COMPUTER) {
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
