<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Models\Attributes\AccountControl;
use DirectoryTree\Watchdog\Dogs\WatchAccountDisable;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenDisabled;

class AccountDisableTest extends DogTestCase
{
    protected $model = Entry::class;

    protected $watchdogs = WatchAccountDisable::class;

    public function test_notification_is_sent()
    {
        Notification::fake();

        $object = Entry::create([
            'cn'                 => 'John Doe',
            'objectclass'        => ['foo'],
            'objectguid'         => $this->faker->uuid,
            'userAccountControl' => [512],
        ]);

        $this->artisan('watchdog:run');

        $object->update(['userAccountControl' => [514]]);

        $this->artisan('watchdog:run');

        Notification::assertSentTo(app(WatchAccountDisable::class), AccountHasBeenDisabled::class);

        $notification = LdapNotification::where([
            'notification' => AccountHasBeenDisabled::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertTrue($notification->sent);
        $this->assertEquals(AccountHasBeenDisabled::class, $notification->notification);
    }

    public function test_notification_is_not_sent_again_after_user_account_control_changes_but_account_is_still_disabled()
    {
        Notification::fake();

        $uac = new AccountControl();
        $uac->accountIsDisabled();

        $object = Entry::create([
            'cn'                 => 'John Doe',
            'objectclass'        => ['foo'],
            'objectguid'         => $this->faker->uuid,
            'userAccountControl' => [$uac],
        ]);

        $this->artisan('watchdog:run');

        $object->update(['userAccountControl' => [$uac->accountIsNormal()]]);

        $this->artisan('watchdog:run');

        Notification::assertNotSentTo(app(WatchAccountDisable::class), AccountHasBeenDisabled::class);
    }
}
