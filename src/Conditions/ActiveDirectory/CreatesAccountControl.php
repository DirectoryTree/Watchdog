<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use Illuminate\Support\Arr;
use LdapRecord\Models\Attributes\AccountControl;

trait CreatesAccountControl
{
    /**
     * The attribute that contains the user account control value.
     *
     * @var string
     */
    protected $attribute = 'userAccountControl';

    /**
     * Creates a new AccountControl object from the given attributes.
     *
     * @param array|null $attributes
     *
     * @return AccountControl
     */
    protected function newUacFromAttributes($attributes)
    {
        return new AccountControl(
            Arr::first($attributes[$this->attribute] ?? [])
        );
    }
}
