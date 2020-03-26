<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

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

        $notifiable = app(WatchPasswordChanges::class);

        $this->expectsNotification($notifiable, PasswordHasChanged::class);

        $object->update(['pwdlastset' => [1000]]);

        $this->artisan('watchdog:monitor');
    }
}
