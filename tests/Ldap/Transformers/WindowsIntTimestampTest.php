<?php

namespace DirectoryTree\Watchdog\Tests\Ldap\Transformers;

use Carbon\Carbon;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Ldap\Transformers\WindowsIntTimestamp;

class WindowsIntTimestampTest extends TestCase
{
    public function test_timestamp_is_converted()
    {
        $transformer = (new WindowsIntTimestamp(['132297066670000000']));

        $this->assertInstanceOf(Carbon::class, $transformer->transform()[0]);
    }

    public function test_invalid_timestamp_is_not_converted()
    {
        $transformer = (new WindowsIntTimestamp(['invalid']));

        $this->assertEquals(['invalid'], $transformer->transform());
    }
}
