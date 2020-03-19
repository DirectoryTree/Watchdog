<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountIsDisabled;
use DirectoryTree\Watchdog\Tests\TestCase;
use LdapRecord\Models\Attributes\AccountControl;

class AccountIsDisabledTest extends TestCase
{
    public function test()
    {
        $this->assertFalse((new AccountIsDisabled(null, null))->passes());
        $this->assertFalse((new AccountIsDisabled([], []))->passes());
        $this->assertFalse((new AccountIsDisabled([0], [0]))->passes());

        $this->assertTrue((new AccountIsDisabled(
            ['userAccountControl' => [0]], ['userAccountControl' => [AccountControl::ACCOUNTDISABLE]]
        ))->passes());
    }
}
