<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use DirectoryTree\Watchdog\Conditions\Condition;

class HasPassword extends Condition
{
    /**
     * @inheritDoc
     */
    public function passes()
    {
        return $this->after->attributes()->has('pwdlastset');
    }
}
