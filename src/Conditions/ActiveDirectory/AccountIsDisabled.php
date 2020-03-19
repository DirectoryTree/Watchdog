<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use LdapRecord\Models\Attributes\AccountControl;
use DirectoryTree\Watchdog\Conditions\Condition;

class AccountIsDisabled extends Condition
{
    use CreatesAccountControl;

    /**
     * Determine if the account is disabled.
     *
     * @return bool
     */
    public function passes()
    {
        $currentUac = $this->newUacFromAttributes($this->after);

        if (
            $currentUac->has(AccountControl::ACCOUNTDISABLE) &&
            empty($before[$this->attribute])
        ) {
            return true;
        }

        $previousUac = $this->newUacFromAttributes($this->before);

        return
            !$previousUac->has(AccountControl::ACCOUNTDISABLE) &&
            $currentUac->has(AccountControl::ACCOUNTDISABLE);
    }
}
