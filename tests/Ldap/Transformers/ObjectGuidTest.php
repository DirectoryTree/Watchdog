<?php

namespace DirectoryTree\Watchdog\Tests\Ldap\Transformers;

use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Ldap\Transformers\ObjectGuid;

class ObjectGuidTest extends TestCase
{
    public function test_valid_guid_is_not_transformed()
    {
        $guid = '270db4d0-249d-46a7-9cc5-eb695d9af9ac';
        $this->assertEquals([$guid], (new ObjectGuid([$guid]))->transform());
    }

    public function test_invalid_guid_is_transformed()
    {
        $guid = 'invalid';
        $this->assertEquals(
            ['69696969-6969-6969-696e-6e76616c6964'],
            (new ObjectGuid([$guid]))->transform()
        );
    }

    public function test_empty_guid_is_not_transformed()
    {
        $this->assertEquals([''], (new ObjectGuid(['']))->transform());
    }

    public function test_binary_guid_is_transformed()
    {
        $hex = 'd0b40d279d24a7469cc5eb695d9af9ac';

        $transformer = new ObjectGuid([hex2bin($hex)]);

        $this->assertEquals(
            ['270db4d0-249d-46a7-9cc5-eb695d9af9ac'],
            $transformer->transform()
        );
    }
}
