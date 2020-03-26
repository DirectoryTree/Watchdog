<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use LdapRecord\Models\Attributes\Timestamp;
use DirectoryTree\Watchdog\LdapNotification;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use DirectoryTree\Watchdog\Dogs\WatchAccountExpiry;
use DirectoryTree\Watchdog\Notifications\AccountHasExpired;

class AccountExpiryTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['watchdog.watchdogs' => [WatchAccountExpiry::class]]);
        config(['watchdog.attributes.transform' => ['accountexpires' => 'windows-int']]);

        DirectoryEmulator::setup();
    }

    public function test()
    {
        $object = Entry::create([
            'cn'                 => 'John Doe',
            'objectclass'        => ['foo'],
            'objectguid'         => $this->faker->uuid,
        ]);

        $this->artisan('watchdog:monitor');

        $watchdog = app(WatchAccountExpiry::class);

        $this->expectsNotification($watchdog, AccountHasExpired::class);

        $timestamp = new Timestamp('windows-int');

        $object->update(['accountexpires' => [$timestamp->fromDateTime(now())]]);

        $this->artisan('watchdog:monitor');

        $notification = LdapNotification::where([
            'notification' => AccountHasExpired::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(AccountHasExpired::class, $notification->notification);
    }
}
