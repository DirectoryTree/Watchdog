<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\AccountHasExpired;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountExpired;

class WatchAccountExpiry extends Watchdog
{
    protected $conditions = [AccountExpired::class];

    public function bark()
    {
        if (!$this->notificationHasBeenSent()) {
            parent::bark();
        }

        if ($this->objectHasDifferentExpiry()) {
            parent::bark();
        }
    }

    public function getName()
    {
        return trans('watchdog::watchdogs.accounts_expired');
    }

    public function getKey()
    {
        return 'watchdog.accounts.expired';
    }

    public function notification()
    {
        return AccountHasExpired::class;
    }

    /**
     * Determine if the object now has a different expiry date than it did previously.
     *
     * @return bool
     */
    protected function objectHasDifferentExpiry()
    {
        if ($change = $this->lastAccountExpiryChange()) {
            return $change->after != $this->currentAccountExpiry();
        }

        return true;
    }

    /**
     * Get the objects current account expiry.
     *
     * @return array
     */
    protected function currentAccountExpiry()
    {
        return $this->object->values['accountexpires'] ?? [];
    }

    /**
     * Get the last account expiry change on the object.
     *
     * @return \DirectoryTree\Watchdog\LdapChange|null
     */
    protected function lastAccountExpiryChange()
    {
        return $this->object->changes()
            ->where('attribute', '=', 'accountexpires')
            ->latest()
            ->skip(1)
            ->first();
    }
}
