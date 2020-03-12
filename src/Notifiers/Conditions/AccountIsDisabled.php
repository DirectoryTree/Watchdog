<?php

namespace DirectoryTree\Watchdog\Notifiers\Conditions;

use LdapRecord\Models\Attributes\AccountControl;

class AccountIsDisabled implements Condition
{
    use CreatesAccountControl;

    /**
     * Determine if the account is disabled.
     *
     * @param array|null $before
     * @param array|null $after
     *
     * @return bool
     */
    public function passes($before, $after)
    {
        $currentUac = $this->newUacFromAttributes($after);

        if (
            $currentUac->has(AccountControl::ACCOUNTDISABLE) &&
            empty($before['userAccountControl'])
        ) {
            return true;
        }

        $previousUac = $this->newUacFromAttributes($before);

        return
            !$previousUac->has(AccountControl::ACCOUNTDISABLE) &&
            $currentUac->has(AccountControl::ACCOUNTDISABLE);
    }
}
