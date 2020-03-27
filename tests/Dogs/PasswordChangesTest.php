<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use LdapRecord\Models\Attributes\Timestamp;
use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use DirectoryTree\Watchdog\Dogs\WatchPasswordChanges;
use DirectoryTree\Watchdog\Notifications\PasswordHasChanged;

class PasswordChangesTest extends DogTestCase
{
    protected $model = Entry::class;

    protected $watchdogs = WatchPasswordChanges::class;

    public function test_notification_is_sent()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'pwdlastset'  => [0],
        ]);

        $this->artisan('watchdog:monitor');

        $timestamp = new Timestamp('windows-int');

        $object->update(['pwdlastset' => [$timestamp->fromDateTime(now())]]);

        $this->artisan('watchdog:monitor');

        Notification::assertSentTo(app(WatchPasswordChanges::class), PasswordHasChanged::class);

        $notification = LdapNotification::where([
            'notification' => PasswordHasChanged::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(PasswordHasChanged::class, $notification->notification);
    }
}
