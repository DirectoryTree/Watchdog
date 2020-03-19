<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\GroupsChanged;
use DirectoryTree\Watchdog\Tests\TestCase;

class GroupsChangedTest extends TestCase
{
    public function test()
    {
        $this->assertFalse((new GroupsChanged(null, null))->passes());
        $this->assertFalse((new GroupsChanged([], []))->passes());
        $this->assertFalse((new GroupsChanged(['foo'], ['bar']))->passes());
        $this->assertFalse((new GroupsChanged(['memberof' => []], ['memberof' => []]))->passes());
        $this->assertFalse((new GroupsChanged(['memberof' => ['foo']], ['memberof' => ['foo']]))->passes());
        $this->assertFalse((new GroupsChanged(['memberof' => ['bar', 'foo']], ['memberof' => ['foo', 'bar']]))->passes());

        $this->assertTrue((new GroupsChanged(['memberof' => ['foo']], ['memberof' => ['bar']]))->passes());
        $this->assertTrue((new GroupsChanged(['memberof' => ['foo', 'bar']], ['memberof' => ['bar']]))->passes());
        $this->assertTrue((new GroupsChanged(['memberof' => ['foo']], ['memberof' => [null]]))->passes());
    }
}
