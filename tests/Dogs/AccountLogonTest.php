<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use LdapRecord\Models\Attributes\Timestamp;
use DirectoryTree\Watchdog\Dogs\WatchAccountLogons;
use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use DirectoryTree\Watchdog\Notifications\AccountLogonHasOccurred;

class AccountLogonTest extends DogTestCase
{
    protected $model = Entry::class;

    protected $watchdogs = WatchAccountLogons::class;

    public function test_notification_is_sent()
    {
        Notification::fake();

        $timestamp = new Timestamp('windows-int');

        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['user'],
            'objectguid'  => $this->faker->uuid,
            'lastlogon'   => [$timestamp->fromDateTime(now()->subMinute())],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['lastlogon' => [$timestamp->fromDateTime(now())]]);

        $this->artisan('watchdog:monitor');

        Notification::assertSentTo(app(WatchAccountLogons::class), AccountLogonHasOccurred::class);

        $notification = LdapNotification::where([
            'notification' => AccountLogonHasOccurred::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertTrue($notification->sent);
        $this->assertEquals(AccountLogonHasOccurred::class, $notification->notification);
    }
}
