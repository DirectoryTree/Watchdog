<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use Illuminate\Support\Arr;
use DirectoryTree\Watchdog\State;
use LdapRecord\Models\Attributes\AccountControl;

trait CreatesAccountControl
{
    /**
     * The attribute that contains the user account control value.
     *
     * @var string
     */
    protected $attribute = 'useraccountcontrol';

    /**
     * Creates a new AccountControl object from the given attributes.
     *
     * @param State $state
     *
     * @return AccountControl
     */
    protected function newAccountControlFromState(State $state)
    {
        return new AccountControl(
            Arr::first($state->attribute($this->attribute))
        );
    }
}
