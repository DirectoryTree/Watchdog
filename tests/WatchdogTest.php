<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\State;
use DirectoryTree\Watchdog\Watchdog;

class WatchdogTest extends TestCase
{
    public function test_name_returns_kebabed_class_name()
    {
        $this->assertEquals('watchdog', (new Watchdog())->getKey());
        $this->assertEquals('test-watchdog-route-key-stub', (new TestWatchdogRouteKeyStub())->getKey());
    }

    public function test_route_key_returns_kebabed_class_name()
    {
        $this->assertEquals('watchdog', (new Watchdog())->getRouteKey());
        $this->assertEquals('test-watchdog-route-key-stub', (new TestWatchdogRouteKeyStub())->getRouteKey());
    }

    public function test_modified_with_empty_states()
    {
        $watchdog = (new Watchdog())->before(new State())->after(new State());
        $this->assertEmpty($watchdog->modified());
        $this->assertEmpty($watchdog->diffBefore());
        $this->assertEmpty($watchdog->diffAfter());
    }

    public function test_modified_with_equal_states()
    {
        $watchdog = (new Watchdog())
            ->before(new State(['foo' => 'bar']))
            ->after(new State(['foo' => 'bar']));

        $this->assertEmpty($watchdog->modified());
        $this->assertEmpty($watchdog->diffBefore());
        $this->assertEmpty($watchdog->diffAfter());
    }

    public function test_modified_with_different_states()
    {
        $watchdog = (new Watchdog())
            ->before(new State(['foo' => 'bar']))
            ->after(new State(['foo' => 'baz', 'bar' => 'baz']));

        $this->assertEquals(['foo', 'bar'], $watchdog->modified());
        $this->assertEquals(['foo'], $watchdog->diffBefore());
        $this->assertEquals(['foo', 'bar'], $watchdog->diffAfter());
    }

    public function test_modified_with_object_values()
    {
        $watchdog = (new Watchdog())
            ->before(new State(['foo' => now()->subHour()]))
            ->after(new State(['foo' => now()->subHours(2)]));

        $this->assertEquals(['foo'], $watchdog->modified());
        $this->assertEquals(['foo'], $watchdog->diffBefore());
        $this->assertEquals(['foo'], $watchdog->diffAfter());
    }

    public function test_modified_with_empty_before_state()
    {
        $watchdog = (new Watchdog())
            ->before(new State([]))
            ->after(new State(['foo' => ['bar']]));

        $this->assertEquals(['foo'], $watchdog->modified());
        $this->assertEquals([], $watchdog->diffBefore());
        $this->assertEquals(['foo'], $watchdog->diffAfter());
    }

    public function test_modified_with_empty_after_state()
    {
        $watchdog = (new Watchdog())
            ->before(new State(['foo' => ['bar']]))
            ->after(new State(['foo' => ['baz']]));

        $this->assertEquals(['foo'], $watchdog->modified());
        $this->assertEquals(['foo'], $watchdog->diffBefore());
        $this->assertEquals(['foo'], $watchdog->diffAfter());
    }
}

class TestWatchdogRouteKeyStub extends Watchdog
{
    //
}
