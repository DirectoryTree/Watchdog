<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use DirectoryTree\Watchdog\Dogs\WatchAccountDisable;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenDisabled;

class AccountDisableTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['watchdog.watchdogs' => [WatchAccountDisable::class]]);

        DirectoryEmulator::setup();
    }

    public function test()
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
            'channels' => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(AccountHasBeenDisabled::class, $notification->notification);
    }
}
