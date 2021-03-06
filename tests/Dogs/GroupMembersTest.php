<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use DirectoryTree\Watchdog\Dogs\WatchGroupMembers;
use DirectoryTree\Watchdog\Notifications\MembersHaveChanged;

class GroupMembersTest extends DogTestCase
{
    protected $model = Entry::class;

    protected $watchdogs = WatchGroupMembers::class;

    public function test_notification_is_sent()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'member'      => ['foo', 'bar'],
        ]);

        $this->artisan('watchdog:run');

        $object->update(['member' => ['foo', 'bar', 'baz']]);

        $this->artisan('watchdog:run');

        Notification::assertSentTo(app(WatchGroupMembers::class), MembersHaveChanged::class);

        $notification = LdapNotification::where([
            'notification' => MembersHaveChanged::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertTrue($notification->sent);
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

        $this->artisan('watchdog:run');

        $object->update(['member' => ['bar', 'foo']]);

        $this->artisan('watchdog:run');

        Notification::assertNotSentTo(app(WatchGroupMembers::class), MembersHaveChanged::class);
    }
}
