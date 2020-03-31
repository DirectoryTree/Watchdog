<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\Watchdog;

class WatchdogTest extends TestCase
{
    public function test_name_returns_kebabed_class_name()
    {
        $this->assertEquals('watchdog', (new Watchdog)->getKey());
        $this->assertEquals('test-watchdog-route-key-stub', (new TestWatchdogRouteKeyStub)->getKey());
    }

    public function test_route_key_returns_kebabed_class_name()
    {
        $this->assertEquals('watchdog', (new Watchdog)->getRouteKey());
        $this->assertEquals('test-watchdog-route-key-stub', (new TestWatchdogRouteKeyStub)->getRouteKey());
    }
}

class TestWatchdogRouteKeyStub extends Watchdog
{
    //
}
