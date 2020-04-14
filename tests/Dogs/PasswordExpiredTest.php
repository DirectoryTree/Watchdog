<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use LdapRecord\Models\Attributes\Timestamp;
use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use DirectoryTree\Watchdog\Dogs\WatchPasswordExpiry;
use DirectoryTree\Watchdog\Notifications\PasswordHasExpired;

class PasswordExpiredTest extends DogTestCase
{
    protected $model = Entry::class;

    protected $watchdogs = WatchPasswordExpiry::class;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the Root DSE record with a two month password expiry time.
        $entry = new Entry([
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'maxpwdage'   => [-51840000000000],
        ]);

        $entry->setDn('dc=local,dc=com')->save();
    }

    public function test_notification_is_sent_when_password_is_expired()
    {
        Notification::fake();

        $timestamp = new Timestamp('windows-int');

        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'pwdlastset'  => [$timestamp->fromDateTime(now())],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['pwdlastset' => [$timestamp->fromDateTime(now()->subMonths(2))]]);

        $this->artisan('watchdog:monitor');

        Notification::assertSentTo(app(WatchPasswordExpiry::class), PasswordHasExpired::class);

        $notification = LdapNotification::where([
            'notification' => PasswordHasExpired::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(2, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertTrue($notification->sent);
        $this->assertEquals(PasswordHasExpired::class, $notification->notification);
    }

    public function test_notification_is_not_sent_when_password_is_not_expired()
    {
        Notification::fake();

        $timestamp = new Timestamp('windows-int');

        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'pwdlastset'  => [$timestamp->fromDateTime(now())],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['pwdlastset' => [$timestamp->fromDateTime(now())]]);

        $this->artisan('watchdog:monitor');

        Notification::assertNotSentTo(app(WatchPasswordExpiry::class), PasswordHasExpired::class);
    }
}
