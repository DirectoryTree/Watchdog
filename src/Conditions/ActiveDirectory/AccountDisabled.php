<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use LdapRecord\Models\Attributes\AccountControl;
use DirectoryTree\Watchdog\Conditions\Condition;

class AccountDisabled extends Condition
{
    use CreatesAccountControl;

    /**
     * Determine if the account is disabled.
     *
     * @return bool
     */
    public function passes()
    {
        if ($this->accountIsCurrentlyDisabled() && $this->accountDidNotHaveAccountControl()) {
            return true;
        }

        return $this->accountIsCurrentlyDisabled() && $this->accountWasNotPreviouslyDisabled();
    }

    /**
     * Determine if the account is currently disabled.
     *
     * @return bool
     */
    protected function accountIsCurrentlyDisabled()
    {
        return $this->newAccountControlFromState($this->after)->has(AccountControl::ACCOUNTDISABLE);
    }

    /**
     * Determine if the account was not previously disabled.
     *
     * @return bool
     */
    protected function accountWasNotPreviouslyDisabled()
    {
        return ! $this->newAccountControlFromState($this->before)->has(AccountControl::ACCOUNTDISABLE);
    }

    /**
     * Determine if the account did not previously have any user account control.
     *
     * @return bool
     */
    protected function accountDidNotHaveAccountControl()
    {
        return empty($this->before->attribute($this->attribute));
    }
}
