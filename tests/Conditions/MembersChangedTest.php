<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\MembersChanged;
use DirectoryTree\Watchdog\Tests\TestCase;

class MembersChangedTest extends TestCase
{
    public function test()
    {
        $this->assertFalse((new MembersChanged(null, null))->passes());
        $this->assertFalse((new MembersChanged([], []))->passes());
        $this->assertFalse((new MembersChanged(['foo'], ['bar']))->passes());
        $this->assertFalse((new MembersChanged(['member' => []], ['member' => []]))->passes());
        $this->assertFalse((new MembersChanged(['member' => ['foo']], ['member' => ['foo']]))->passes());
        $this->assertFalse((new MembersChanged(['member' => ['bar', 'foo']], ['member' => ['foo', 'bar']]))->passes());

        $this->assertTrue((new MembersChanged(['member' => ['foo']], ['member' => ['bar']]))->passes());
        $this->assertTrue((new MembersChanged(['member' => ['foo', 'bar']], ['member' => ['bar']]))->passes());
        $this->assertTrue((new MembersChanged(['member' => ['foo']], ['member' => [null]]))->passes());
    }
}
