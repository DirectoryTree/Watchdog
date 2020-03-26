<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\State;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\PasswordChanged;

class PasswordChangedTest extends TestCase
{
    public function test()
    {
        $this->assertFalse($this->newCondition([], [])->passes());
        $this->assertFalse($this->newCondition(['pwdlastset' => []], ['pwdlastset' => []])->passes());

        $this->assertTrue($this->newCondition(['pwdlastset' => ['0']], ['pwdlastset' => ['10000']])->passes());
    }

    protected function newCondition($before = [], $after = [])
    {
        return new PasswordChanged(new State($before), new State($after));
    }
}
