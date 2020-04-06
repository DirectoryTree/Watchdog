<?php

namespace DirectoryTree\Watchdog\Tests\Ldap\Transformers;

use Carbon\Carbon;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Ldap\Transformers\WindowsTimestamp;

class WindowsTimestampTest extends TestCase
{
    public function test_timestamp_is_converted()
    {
        $transformer = (new WindowsTimestamp(['20200326143107.0Z']));

        $this->assertInstanceOf(Carbon::class, $transformer->transform()[0]);
        $this->assertEquals('UTC', $transformer->transform()[0]->getTimezone()->getName());
    }

    public function test_invalid_timestamp_is_not_converted()
    {
        $transformer = (new WindowsTimestamp(['invalid']));

        $this->assertEquals(['invalid'], $transformer->transform());
    }
}
