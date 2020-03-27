<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Models\Attributes\AccountControl;
use DirectoryTree\Watchdog\Dogs\WatchAccountEnable;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenEnabled;

class AccountEnableTest extends DogTestCase
{
    protected $model = Entry::class;

    protected $watchdogs = WatchAccountEnable::class;

    public function test_notification_is_sent()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'                 => 'John Doe',
            'objectclass'        => ['foo'],
            'objectguid'         => $this->faker->uuid,
            'userAccountControl' => [514],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['userAccountControl' => [512]]);

        $this->artisan('watchdog:monitor');

        Notification::assertSentTo(app(WatchAccountEnable::class), AccountHasBeenEnabled::class);

        $notification = LdapNotification::where([
            'notification' => AccountHasBeenEnabled::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(AccountHasBeenEnabled::class, $notification->notification);
    }

    public function test_notification_is_not_sent_again_after_user_account_control_changes_but_account_is_still_enabled()
    {
        Notification::fake();

        $uac = new AccountControl();
        $uac->accountIsNormal();

        $object = Entry::create([
            'cn'                 => 'John Doe',
            'objectclass'        => ['foo'],
            'objectguid'         => $this->faker->uuid,
            'userAccountControl' => [$uac],
        ]);

        $this->artisan('watchdog:monitor');

        $object->update(['userAccountControl' => [$uac->accountIsForWorkstation()]]);

        $this->artisan('watchdog:monitor');

        Notification::assertNotSentTo(app(WatchAccountEnable::class), AccountHasBeenEnabled::class);
    }
}
