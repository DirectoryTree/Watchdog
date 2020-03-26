<?php

namespace DirectoryTree\Watchdog\Tests\Ldap\Transformers;

use Carbon\Carbon;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Ldap\Transformers\LdapTimestamp;

class TimestampTest extends TestCase
{
    public function test_timestamp_is_converted()
    {
        $transformer = (new LdapTimestamp(['20200326143107Z']));

        $this->assertInstanceOf(Carbon::class, $transformer->transform()[0]);
    }

    public function test_invalid_timestamp_is_not_converted()
    {
        $transformer = (new LdapTimestamp(['invalid']));

        $this->assertEquals(['invalid'], $transformer->transform());
    }
}
