<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use LdapRecord\Models\Attributes\Timestamp;
use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use DirectoryTree\Watchdog\Dogs\WatchComputerLogons;
use DirectoryTree\Watchdog\Notifications\ComputerLoginHasOccurred;

class ComputerLogonTest extends DogTestCase
{
    protected $model = Entry::class;

    protected $watchdogs = WatchComputerLogons::class;

    public function test_notification_is_sent()
    {
        Notification::fake();

        $timestamp = new Timestamp('windows-int');

        $object = Entry::create([
            'cn'          => 'SERV-01',
            'objectclass' => ['computer'],
            'objectguid'  => $this->faker->uuid,
            'lastlogon'   => [$timestamp->fromDateTime(now()->subMinute())],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['lastlogon' => [$timestamp->fromDateTime(now())]]);

        $this->artisan('watchdog:monitor');

        Notification::assertSentTo(app(WatchComputerLogons::class), ComputerLoginHasOccurred::class);

        $notification = LdapNotification::where([
            'notification' => ComputerLoginHasOccurred::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertTrue($notification->sent);
        $this->assertEquals(ComputerLoginHasOccurred::class, $notification->notification);
    }
}
