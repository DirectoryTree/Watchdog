<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\Dogs\WatchLogins;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use DirectoryTree\Watchdog\Notifications\LoginHasOccurred;

class LoginsTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['watchdog.watchdogs' => [WatchLogins::class]]);

        DirectoryEmulator::setup();
    }

    public function test()
    {
        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'lastlogon'   => now()->subMinute(),
        ]);

        $this->artisan('watchdog:monitor');

        $notifiable = app(WatchLogins::class);

        $this->expectsNotification($notifiable, LoginHasOccurred::class);

        $object->update(['lastlogon' => now()]);

        $this->artisan('watchdog:monitor');
    }
}
