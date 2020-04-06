<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Ldap\TypeResolver;
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
        return AccountLogonHasOccurred::class;
    }
}
