<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\State;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\GroupsChanged;
use DirectoryTree\Watchdog\Tests\TestCase;

class GroupsChangedTest extends TestCase
{
    public function test()
    {
        $this->assertFalse($this->newCondition(null, null)->passes());
        $this->assertFalse($this->newCondition([], [])->passes());
        $this->assertFalse($this->newCondition(['foo'], ['bar'])->passes());
        $this->assertFalse($this->newCondition(['memberof' => []], ['memberof' => []])->passes());
        $this->assertFalse($this->newCondition(['memberof' => ['foo']], ['memberof' => ['foo']])->passes());
        $this->assertFalse($this->newCondition(['memberof' => ['bar', 'foo']], ['memberof' => ['foo', 'bar']])->passes());

        $this->assertTrue($this->newCondition(['memberof' => ['foo']], ['memberof' => ['bar']])->passes());
        $this->assertTrue($this->newCondition(['memberof' => ['foo', 'bar']], ['memberof' => ['bar']])->passes());
        $this->assertTrue($this->newCondition(['memberof' => ['foo']], ['memberof' => [null]])->passes());
    }

    protected function newCondition($before = [], $after = [])
    {
        return new GroupsChanged(new State($before), new State($after));
    }
}
