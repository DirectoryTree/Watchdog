<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use LdapRecord\Models\ActiveDirectory\Entry;
use DirectoryTree\Watchdog\Dogs\WatchMemberships;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use DirectoryTree\Watchdog\Notifications\MembersHaveChanged;

class MembershipsTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['watchdog.watchdogs' => [WatchMemberships::class]]);

        DirectoryEmulator::setup();
    }

    public function test()
    {
        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'memberof'    => ['foo', 'bar'],
        ]);

        $this->artisan('watchdog:monitor');

        $notifiable = app(WatchMemberships::class);

        $this->expectsNotification($notifiable, MembersHaveChanged::class);

        $object->update(['memberof' => ['foo', 'bar', 'baz']]);

        $this->artisan('watchdog:monitor');
    }
}
