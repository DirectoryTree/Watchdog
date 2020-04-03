<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Ldap\TypeGuesser;
use DirectoryTree\Watchdog\Notifications\AccountLogonHasOccurred;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\NewLogin;

class WatchAccountLogons extends Watchdog
{
    /**
     * The watchdog conditions.
     *
     * @var array
     */
    protected $conditions = [NewLogin::class];

    /**
     * {@inheritDoc}
     */
    public function bark()
    {
        if ($this->object->type == TypeGuesser::TYPE_USER) {
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
        return AccountLogonHasOccurred::class;
    }
}
