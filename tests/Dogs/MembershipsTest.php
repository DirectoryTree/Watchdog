<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use LdapRecord\Models\ActiveDirectory\Entry;
use DirectoryTree\Watchdog\Dogs\WatchMemberships;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use Illuminate\Support\Facades\Notification;
use DirectoryTree\Watchdog\Notifications\MembersHaveChanged;

class MembershipsTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $model = Entry::class;

        config(["watchdog.watch.$model" => [WatchMemberships::class]]);

        DirectoryEmulator::setup();
    }

    public function test_notification_is_sent()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'memberof'    => ['foo', 'bar'],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['memberof' => ['foo', 'bar', 'baz']]);

        $this->artisan('watchdog:monitor');

        Notification::assertSentTo(app(WatchMemberships::class), MembersHaveChanged::class);

        $notification = LdapNotification::where([
            'notification' => MembersHaveChanged::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(MembersHaveChanged::class, $notification->notification);
    }

    public function test_notification_is_not_sent_again_when_member_of_is_reordered()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'memberof'    => ['foo', 'bar'],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['memberof' => ['bar', 'foo']]);

        $this->artisan('watchdog:monitor');

        Notification::assertNotSentTo(app(WatchMemberships::class), MembersHaveChanged::class);
    }
}
