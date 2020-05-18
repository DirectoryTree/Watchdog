<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use LdapRecord\Models\Attributes\Timestamp;
use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use DirectoryTree\Watchdog\Dogs\WatchAccountLockout;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenLocked;

class AccountLockoutTest extends DogTestCase
{
    protected $model = Entry::class;

    protected $watchdogs = WatchAccountLockout::class;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('watchdog.attributes.transform', ['lockouttime' => 'windows-int']);
    }

    public function test_notification_is_sent()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'            => 'John Doe',
            'objectclass'   => ['foo'],
            'objectguid'    => $this->faker->uuid,
        ]);

        $this->artisan('watchdog:run');

        $timestamp = new Timestamp('windows-int');

        $object->update(['lockouttime' => [$timestamp->fromDateTime(now())]]);

        $this->artisan('watchdog:run');

        Notification::assertSentTo(app(WatchAccountLockout::class), AccountHasBeenLocked::class);

        $notification = LdapNotification::where([
            'notification' => AccountHasBeenLocked::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertTrue($notification->sent);
        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(AccountHasBeenLocked::class, $notification->notification);
    }

    public function test_notification_is_not_sent_when_a_user_is_already_locked_out()
    {
        Notification::fake();

        $timestamp = new Timestamp('windows-int');

        Entry::create([
            'cn'                => 'John Doe',
            'objectclass'       => ['foo'],
            'objectguid'        => $this->faker->uuid,
            'accountexpires'    => [$timestamp->fromDateTime(now())],
        ]);

        $this->artisan('watchdog:run');

        Notification::assertNotSentTo(app(WatchAccountLockout::class), AccountHasBeenLocked::class);
    }
}
