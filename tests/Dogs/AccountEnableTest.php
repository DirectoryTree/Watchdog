<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\Dogs\WatchAccountEnable;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenEnabled;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use LdapRecord\Models\ActiveDirectory\Entry;

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
            'cn' => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid' => $this->faker->uuid,
            'userAccountControl' => [514]
        ]);

        $this->artisan('watchdog:monitor');

        $notifiable = app(WatchAccountEnable::class);

        $this->expectsNotification($notifiable, AccountHasBeenEnabled::class);

        $object->update(['userAccountControl' => [512]]);

        $this->artisan('watchdog:monitor');
    }
}
