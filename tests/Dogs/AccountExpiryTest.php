<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use LdapRecord\Models\Attributes\Timestamp;
use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use DirectoryTree\Watchdog\Dogs\WatchAccountExpiry;
use DirectoryTree\Watchdog\Notifications\AccountHasExpired;

class AccountExpiryTest extends DogTestCase
{
    protected $model = Entry::class;

    protected $watchdogs = WatchAccountExpiry::class;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('watchdog.attributes.transform', ['accountexpires' => 'windows-int']);
    }

    public function test_notification_is_sent()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'            => 'John Doe',
            'objectclass'   => ['foo'],
            'objectguid'    => $this->faker->uuid,
        ]);

        $this->artisan('watchdog:monitor');

        $timestamp = new Timestamp('windows-int');

        $object->update(['accountexpires' => [$timestamp->fromDateTime(now())]]);

        $this->artisan('watchdog:monitor');

        Notification::assertSentTo(app(WatchAccountExpiry::class), AccountHasExpired::class);

        $notification = LdapNotification::where([
            'notification' => AccountHasExpired::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertTrue($notification->sent);
        $this->assertEquals(AccountHasExpired::class, $notification->notification);
    }

    public function test_notification_is_not_sent_when_account_has_not_yet_expired()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'            => 'John Doe',
            'objectclass'   => ['foo'],
            'objectguid'    => $this->faker->uuid,
        ]);

        $this->artisan('watchdog:monitor');

        $timestamp = new Timestamp('windows-int');

        $object->update(['accountexpires' => [$timestamp->fromDateTime(now()->addHour())]]);

        $this->artisan('watchdog:monitor');

        Notification::assertNotSentTo(app(WatchAccountExpiry::class), AccountHasExpired::class);
    }

    public function test_notification_is_not_sent_when_a_user_is_already_expired()
    {
        Notification::fake();

        $timestamp = new Timestamp('windows-int');

        Entry::create([
            'cn'                => 'John Doe',
            'objectclass'       => ['foo'],
            'objectguid'        => $this->faker->uuid,
            'accountexpires'    => [$timestamp->fromDateTime(now())],
        ]);

        $this->artisan('watchdog:monitor');

        Notification::assertNotSentTo(app(WatchAccountExpiry::class), AccountHasExpired::class);
    }
}
