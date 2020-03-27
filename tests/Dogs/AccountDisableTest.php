<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use DirectoryTree\Watchdog\Dogs\WatchAccountDisable;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenDisabled;
use LdapRecord\Models\Attributes\AccountControl;

class AccountDisableTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $model = Entry::class;

        config(["watchdog.watch.$model" => [WatchAccountDisable::class]]);

        DirectoryEmulator::setup();
    }

    public function test_notification_is_sent()
    {
        $object = Entry::create([
            'cn'                 => 'John Doe',
            'objectclass'        => ['foo'],
            'objectguid'         => $this->faker->uuid,
            'userAccountControl' => [512],
        ]);

        $this->artisan('watchdog:monitor');

        $watchdog = app(WatchAccountDisable::class);

        $this->expectsNotification($watchdog, AccountHasBeenDisabled::class);

        $object->update(['userAccountControl' => [514]]);

        $this->artisan('watchdog:monitor');

        $notification = LdapNotification::where([
            'notification' => AccountHasBeenDisabled::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
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

        $this->artisan('watchdog:monitor');

        $object->update(['userAccountControl' => [$uac->accountIsNormal()]]);

        $this->artisan('watchdog:monitor');

        Notification::assertNotSentTo(app(WatchAccountDisable::class), AccountHasBeenDisabled::class);
    }
}
