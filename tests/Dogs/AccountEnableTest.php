<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use DirectoryTree\Watchdog\Dogs\WatchAccountEnable;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenEnabled;

class AccountEnableTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['watchdog.watchdogs' => [WatchAccountEnable::class]]);

        DirectoryEmulator::setup();
    }

    public function test()
    {
        $object = Entry::create([
            'cn'                 => 'John Doe',
            'objectclass'        => ['foo'],
            'objectguid'         => $this->faker->uuid,
            'userAccountControl' => [514],
        ]);

        $this->artisan('watchdog:monitor');

        $watchdog = app(WatchAccountEnable::class);

        $this->expectsNotification($watchdog, AccountHasBeenEnabled::class);

        $object->update(['userAccountControl' => [512]]);

        $this->artisan('watchdog:monitor');

        $notification = LdapNotification::where([
            'notification' => AccountHasBeenEnabled::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(AccountHasBeenEnabled::class, $notification->notification);
    }
}
