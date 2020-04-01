<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\State;
use DirectoryTree\Watchdog\Tests\TestCase;
use LdapRecord\Models\Attributes\AccountControl;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountEnabled;

class AccountEnabledTest extends TestCase
{
    public function test()
    {
        $this->assertFalse($this->newCondition(null, null)->passes());
        $this->assertFalse($this->newCondition([], [])->passes());
        $this->assertFalse($this->newCondition([0], [0])->passes());

        $this->assertFalse(
            $this->newCondition(['userAccountControl' => [0]], ['userAccountControl' => [AccountControl::NORMAL_ACCOUNT]])->passes()
        );

        $this->assertTrue(
            $this->newCondition(['userAccountControl' => [
                AccountControl::NORMAL_ACCOUNT + AccountControl::ACCOUNTDISABLE,
            ]], ['userAccountControl' => [
                AccountControl::NORMAL_ACCOUNT,
            ]])->passes()
        );
    }

    protected function newCondition($before = [], $after = [])
    {
        return new AccountEnabled(new State($before), new State($after));
    }
}
