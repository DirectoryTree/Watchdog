<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\Dogs\WatchAccountDisable;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenDisabled;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use LdapRecord\Models\ActiveDirectory\Entry;

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
            'cn' => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid' => $this->faker->uuid,
            'userAccountControl' => [512]
        ]);

        $this->artisan('watchdog:monitor');

        $notifiable = app(WatchAccountDisable::class);

        $this->expectsNotification($notifiable, AccountHasBeenDisabled::class);

        $object->update(['userAccountControl' => [514]]);

        $this->artisan('watchdog:monitor');
    }
}
