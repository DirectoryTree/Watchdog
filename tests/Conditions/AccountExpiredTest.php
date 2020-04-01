<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\State;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountExpired;

class AccountExpiredTest extends TestCase
{
    public function test()
    {
        $this->assertFalse($this->newCondition(null, null)->passes());
        $this->assertFalse($this->newCondition([], [])->passes());
        $this->assertFalse($this->newCondition([0], [0])->passes());

        $expiry = now()->subHour();

        $this->assertFalse(
            $this->newCondition(['accountexpires' => [$expiry]], ['accountexpires' => [$expiry]])->passes()
        );

        $this->assertTrue(
            $this->newCondition(['accountexpires' => ['']], ['accountexpires' => [$expiry]])->passes()
        );
    }

    protected function newCondition($before = [], $after = [])
    {
        return new AccountExpired(new State($before), new State($after));
    }
}
