<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use DirectoryTree\Watchdog\Dogs\WatchPasswordChanges;
use DirectoryTree\Watchdog\Notifications\PasswordHasChanged;

class PasswordChangesTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['watchdog.watchdogs' => [WatchPasswordChanges::class]]);

        DirectoryEmulator::setup();
    }

    public function test()
    {
        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'pwdlastset'  => [0],
        ]);

        $this->artisan('watchdog:monitor');

        $watchdog = app(WatchPasswordChanges::class);

        $this->expectsNotification($watchdog, PasswordHasChanged::class);

        $object->update(['pwdlastset' => [1000]]);

        $this->artisan('watchdog:monitor');

        $notification = LdapNotification::where([
            'notification' => PasswordHasChanged::class,
            'channels' => json_encode(['mail']),
        ])->first();

        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(PasswordHasChanged::class, $notification->notification);
    }
}
