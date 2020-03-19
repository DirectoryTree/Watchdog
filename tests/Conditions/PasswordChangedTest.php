<?php


namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\PasswordChanged;
use DirectoryTree\Watchdog\Tests\TestCase;

class PasswordChangedTest extends TestCase
{
    public function test()
    {
        $this->assertFalse((new PasswordChanged([], []))->passes());
        $this->assertFalse((new PasswordChanged(['pwdlastset' => []], ['pwdlastset' => []]))->passes());

        $this->assertTrue((new PasswordChanged(['pwdlastset' => ['0']], ['pwdlastset' => ['10000']]))->passes());
    }
}
