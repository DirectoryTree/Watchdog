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
        $previousExpiryDate = $this->accountsPreviousExpiry();

        if (($currentExpiryDate = $this->accountsCurrentExpiry()) && $currentExpiryDate instanceof Carbon) {
            // We will ensure the expiry date has been changed before
            // allowing the condition to pass. Otherwise, we will
            // simply check if the current expiry has passed.
            return $previousExpiryDate instanceof Carbon ?
                !$currentExpiryDate->eq($previousExpiryDate) :
                $currentExpiryDate->isPast();
        }

        return false;
    }

    /**
     * Get the accounts previous expiry date.
     *
     * @return null|string|Carbon
     */
    protected function accountsPreviousExpiry()
    {
        return $this->before->attribute('accountexpires')->first();
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
