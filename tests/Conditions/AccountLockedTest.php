<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountLocked;
use DirectoryTree\Watchdog\State;
use DirectoryTree\Watchdog\Tests\TestCase;

class AccountLockedTest extends TestCase
{
    public function test()
    {
        $this->assertFalse($this->newCondition(null, null)->passes());
        $this->assertFalse($this->newCondition([], [])->passes());
        $this->assertFalse($this->newCondition([0], [0])->passes());

        $lockout = now();

        $this->assertFalse(
            $this->newCondition(['lockouttime' => [$lockout]], ['lockouttime' => [$lockout]])->passes()
        );

        $this->assertTrue(
            $this->newCondition(['lockouttime' => []], ['lockouttime' => [$lockout]])->passes()
        );
    }

    protected function newCondition($before = [], $after = [])
    {
        return new AccountLocked(new State($before), new State($after));
    }
}
