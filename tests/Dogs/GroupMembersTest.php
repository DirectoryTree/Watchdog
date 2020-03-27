<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
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

    public function test_notification_is_sent()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'member'      => ['foo', 'bar'],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['member' => ['foo', 'bar', 'baz']]);

        $this->artisan('watchdog:monitor');

        Notification::assertSentTo(app(WatchGroupMembers::class), MembersHaveChanged::class);

        $notification = LdapNotification::where([
            'notification' => MembersHaveChanged::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(MembersHaveChanged::class, $notification->notification);
    }

    public function test_notification_is_not_sent_again_when_members_are_reordered()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'member'      => ['foo', 'bar'],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['member' => ['bar', 'foo']]);

        $this->artisan('watchdog:monitor');

        Notification::assertNotSentTo(app(WatchGroupMembers::class), MembersHaveChanged::class);
    }
}
