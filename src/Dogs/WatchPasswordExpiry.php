<?php

namespace DirectoryTree\Watchdog\Dogs;

use Carbon\Carbon;
use LdapRecord\Utilities;
use DirectoryTree\Watchdog\State;
use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\Notifications\PasswordHasExpired;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\HasPassword;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountHasExpiringPassword;

class WatchPasswordExpiry extends Watchdog
{
    /**
     * The watchdog conditions.
     *
     * @var array
     */
    protected $conditions = [
        HasPassword::class,
        AccountHasExpiringPassword::class,
    ];

    /**
     * {@inheritDoc}
     */
    public function bark()
    {
        if ($this->notificationHasBeenSent()) {
            return;
        }

        if ($this->passwordHasExpired($this->getRootDseObject())) {
            parent::bark();
        }
    }

    /**
     * Determine if the users password has expired.
     *
     * @param LdapObject|null $rootDse
     *
     * @return bool
     */
    protected function passwordHasExpired(LdapObject $rootDse = null)
    {
        if (is_null($rootDse)) {
            return false;
        }

        if ($maxPasswordAge = $this->getMaxPasswordAgeInteger($rootDse)) {
            $passwordLastSet = $this->getPasswordLastSetValue();

            // Convert from 100 nanosecond ticks to seconds.
            // This will always be a negative number.
            $maxPasswordAgeSeconds = $maxPasswordAge / 10000000;

            $lastSetUnixEpoch = $passwordLastSet instanceof Carbon
                ? $passwordLastSet->timestamp
                : Utilities::convertWindowsTimeToUnixTime($passwordLastSet);

            $passwordExpiryTime = $lastSetUnixEpoch - $maxPasswordAgeSeconds;

            return now()->greaterThanOrEqualTo(
                now()->setTimestamp($passwordExpiryTime)
            );
        }

        return false;
    }

    /**
     * Get the max password age integer from the root DSE object.
     *
     * @param LdapObject $rootDse
     *
     * @return mixed
     */
    protected function getMaxPasswordAgeInteger(LdapObject $rootDse)
    {
        return (new State($rootDse->values))->attribute('maxpwdage')->first();
    }

    /**
     * Attempt to retrieve the root DSE object.
     *
     * @return \DirectoryTree\Watchdog\LdapObject|null
     */
    protected function getRootDseObject()
    {
        return $this->object->roots()->first();
    }

    /**
     * Get the objects password last set integer value.
     *
     * @return int|null
     */
    protected function getPasswordLastSetValue()
    {
        return $this->after->attribute('pwdlastset')->first();
    }

    /**
     * The watchdog notification.
     *
     * @return string
     */
    public function notification()
    {
        return PasswordHasExpired::class;
    }
}
