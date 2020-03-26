<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use Carbon\Carbon;
use DirectoryTree\Watchdog\Conditions\Condition;

class AccountExpired extends Condition
{
    /**
     * Determine if the account has expired.
     *
     * @return bool
     */
    public function passes()
    {
        if (($date = $this->accountsCurrentExpiry()) && $date instanceof Carbon) {
            return $date->isPast();
        }

        return false;
    }

    /**
     * Get the accounts current expiry date.
     *
     * @return null|string|Carbon
     */
    protected function accountsCurrentExpiry()
    {
        return $this->after->attribute('accountexpires')->first();
    }
}
