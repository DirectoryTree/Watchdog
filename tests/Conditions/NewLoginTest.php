<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\State;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\NewLogin;

class NewLoginTest extends TestCase
{
    public function test()
    {
        $this->assertFalse($this->newCondition(null, null)->passes());
        $this->assertFalse($this->newCondition([], [])->passes());
        $this->assertFalse($this->newCondition(['foo'], ['bar'])->passes());

        $lastLogin = now();

        $this->assertFalse($this->newCondition(
            ['lastlogon' => [$lastLogin]],
            ['lastlogon' => [$lastLogin]]
        )->passes());

        $this->assertTrue($this->newCondition(
            ['lastlogon' => [now()->subDay()]],
            ['lastlogon' => [$lastLogin]]
        )->passes());

        $this->assertTrue($this->newCondition(
            ['lastlogon' => []],
            ['lastlogon' => [$lastLogin]]
        )->passes());
    }

    protected function newCondition($before = [], $after = [])
    {
        return new NewLogin(new State($before), new State($after));
    }
}
