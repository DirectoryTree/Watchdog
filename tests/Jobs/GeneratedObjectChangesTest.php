<?php

namespace DirectoryTree\Watchdog\Tests\Jobs;

use DirectoryTree\Watchdog\Jobs\GenerateObjectChanges;
use DirectoryTree\Watchdog\LdapChange;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\Tests\TestCase;

class GeneratedObjectChangesTest extends TestCase
{
    public function test_object_changes_are_generated()
    {
        $modified = [
            'foo' => ['one'],
            'bar' => ['two'],
        ];

        $object = factory(LdapObject::class)->create([
            'values' => $modified,
        ]);

        GenerateObjectChanges::dispatch($object, now(), $modified, $old = []);

        $changes = LdapChange::get();

        $this->assertCount(2, $changes);
        $this->assertEquals('foo', $changes->first()->attribute);
        $this->assertEmpty($changes->first()->before);
        $this->assertEquals('one', $changes->first()->after[0]);

        $this->assertEquals('bar', $changes->last()->attribute);
        $this->assertEmpty($changes->last()->before);
        $this->assertEquals('two', $changes->last()->after[0]);
    }
}
