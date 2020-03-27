<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use LdapRecord\Models\Attributes\Timestamp;
use DirectoryTree\Watchdog\Dogs\WatchLogins;
use DirectoryTree\Watchdog\LdapNotification;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use Illuminate\Support\Facades\Notification;
use DirectoryTree\Watchdog\Notifications\LoginHasOccurred;

class LoginsTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $model = Entry::class;

        config(["watchdog.watch.$model" => [WatchLogins::class]]);

        DirectoryEmulator::setup();
    }

    public function test_notification_is_sent()
    {
        Notification::fake();

        $timestamp = new Timestamp('windows-int');

        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'lastlogon'   => [$timestamp->fromDateTime(now()->subMinute())],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['lastlogon' => [$timestamp->fromDateTime(now())]]);

        $this->artisan('watchdog:monitor');

        Notification::assertSentTo(app(WatchLogins::class), LoginHasOccurred::class);

        $notification = LdapNotification::where([
            'notification' => LoginHasOccurred::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(LoginHasOccurred::class, $notification->notification);
    }
}
