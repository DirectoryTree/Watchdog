<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Ldap\TypeGuesser;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\NewLogin;
use DirectoryTree\Watchdog\Notifications\AccountLogonHasOccurred;

class WatchAccountLogons extends Watchdog
{
    /**
     * The watchdog conditions.
     *
     * @var array
     */
    protected $conditions = [NewLogin::class];

    /**
     * {@inheritdoc}
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
