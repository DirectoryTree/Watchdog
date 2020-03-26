<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use DirectoryTree\Watchdog\Conditions\Condition;
use LdapRecord\Models\Attributes\AccountControl;

class AccountEnabled extends Condition
{
    use CreatesAccountControl;

    /**
     * Determine if the account has been enabled.
     *
     * @return bool
     */
    public function passes()
    {
        return $this->accountWasPreviouslyDisabled() && $this->accountIsNowEnabled();
    }

    /**
     * Determine if the account was previously disabled.
     *
     * @return bool
     */
    protected function accountWasPreviouslyDisabled()
    {
        return $this->newAccountControlFromState($this->before)->has(AccountControl::ACCOUNTDISABLE);
    }

    /**
     * Determine if the account is now enabled.
     *
     * @return bool
     */
    protected function accountIsNowEnabled()
    {
        return !$this->newAccountControlFromState($this->after)->has(AccountControl::ACCOUNTDISABLE);
    }
}
