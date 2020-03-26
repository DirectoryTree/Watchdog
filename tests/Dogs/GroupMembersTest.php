<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use DirectoryTree\Watchdog\Dogs\WatchGroupMembers;
use DirectoryTree\Watchdog\Notifications\MembersHaveChanged;

class GroupMembersTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $model = Entry::class;

        config(["watchdog.watch.$model" => [WatchGroupMembers::class]]);

        DirectoryEmulator::setup();
    }

    public function test()
    {
        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'member'      => ['foo', 'bar'],
        ]);

        $this->artisan('watchdog:monitor');

        $watchdog = app(WatchGroupMembers::class);

        $this->expectsNotification($watchdog, MembersHaveChanged::class);

        $object->update(['member' => ['foo', 'bar', 'baz']]);

        $this->artisan('watchdog:monitor');

        $notification = LdapNotification::where([
            'notification' => MembersHaveChanged::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(MembersHaveChanged::class, $notification->notification);
    }
}
