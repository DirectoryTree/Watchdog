<?php

namespace DirectoryTree\Watchdog\Tests\Ldap\Transformers;

use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Ldap\Transformers\ObjectSid;

class ObjectSidTest extends TestCase
{
    public function test_valid_sid_is_not_transformed()
    {
        $sid = 'S-1-5-21-2562418665-3218585558-1813906818-1576';
        $this->assertEquals([$sid], (new ObjectSid([$sid]))->transform());
    }

    public function test_invalid_sid_is_not_transformed()
    {
        $sid = 'invalid';
        $this->assertEquals([$sid], (new ObjectSid([$sid]))->transform());
    }

    public function test_binary_sid_is_transformed()
    {
        $hex = '010500000000000515000000dcf4dc3b833d2b46828ba62800020000';

        $transformer = (new ObjectSid([hex2bin($hex)]));

        $this->assertEquals(
            ['S-1-5-21-1004336348-1177238915-682003330-512'],
            $transformer->transform()
        );
    }
}
