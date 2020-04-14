<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use DirectoryTree\Watchdog\Conditions\Condition;
use LdapRecord\Models\Attributes\AccountControl;

class AccountHasExpiringPassword extends Condition
{
    use CreatesAccountControl;

    /**
     * {@inheritdoc}
     */
    public function passes()
    {
        return !$this->newAccountControlFromState($this->after)->has(AccountControl::DONT_EXPIRE_PASSWORD);
    }
}
