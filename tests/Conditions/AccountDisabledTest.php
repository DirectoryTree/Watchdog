<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\State;
use DirectoryTree\Watchdog\Tests\TestCase;
use LdapRecord\Models\Attributes\AccountControl;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountDisabled;

class AccountDisabledTest extends TestCase
{
    public function test()
    {
        $this->assertFalse($this->newCondition(null, null)->passes());
        $this->assertFalse($this->newCondition([], [])->passes());
        $this->assertFalse($this->newCondition([0], [0])->passes());

        $this->assertTrue(
            $this->newCondition(['userAccountControl' => [0]], ['userAccountControl' => [AccountControl::ACCOUNTDISABLE]])->passes()
        );
    }

    protected function newCondition($before = [], $after = [])
    {
        return new AccountDisabled(new State($before), new State($after));
    }
}
