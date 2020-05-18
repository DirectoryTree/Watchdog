<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use DirectoryTree\Watchdog\Conditions\Condition;
use LdapRecord\Models\Attributes\AccountControl;

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
        return
            $this->before->attributes()->isNotEmpty() &&
            $this->accountWasNotPreviouslyDisabled() &&
            $this->accountIsCurrentlyDisabled();
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
        return !$this->newAccountControlFromState($this->before)->has(AccountControl::ACCOUNTDISABLE);
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
