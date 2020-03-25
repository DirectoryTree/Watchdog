<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\MembersChanged;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\State;

class MembersChangedTest extends TestCase
{
    public function test()
    {
        $this->assertFalse($this->newCondition(null, null)->passes());
        $this->assertFalse($this->newCondition([], [])->passes());
        $this->assertFalse($this->newCondition(['foo'], ['bar'])->passes());
        $this->assertFalse($this->newCondition(['member' => []], ['member' => []])->passes());
        $this->assertFalse($this->newCondition(['member' => ['foo']], ['member' => ['foo']])->passes());
        $this->assertFalse($this->newCondition(['member' => ['bar', 'foo']], ['member' => ['foo', 'bar']])->passes());

        $this->assertTrue(($this->newCondition(['member' => ['foo']], ['member' => ['bar']]))->passes());
        $this->assertTrue(($this->newCondition(['member' => ['foo', 'bar']], ['member' => ['bar']]))->passes());
        $this->assertTrue(($this->newCondition(['member' => ['foo']], ['member' => [null]]))->passes());
    }

    protected function newCondition($before = [], $after = [])
    {
        return new MembersChanged(new State($before), new State($after));
    }
}
