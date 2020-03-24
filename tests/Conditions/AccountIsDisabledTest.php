<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountDisabled;
use DirectoryTree\Watchdog\Tests\TestCase;
use LdapRecord\Models\Attributes\AccountControl;

class AccountIsDisabledTest extends TestCase
{
    public function test()
    {
        $this->assertFalse((new AccountDisabled(null, null))->passes());
        $this->assertFalse((new AccountDisabled([], []))->passes());
        $this->assertFalse((new AccountDisabled([0], [0]))->passes());

        $this->assertTrue((new AccountDisabled(
            ['userAccountControl' => [0]], ['userAccountControl' => [AccountControl::ACCOUNTDISABLE]]
        ))->passes());
    }
}
