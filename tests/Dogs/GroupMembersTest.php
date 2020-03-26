<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;
use DirectoryTree\Watchdog\Dogs\WatchGroupMembers;
use DirectoryTree\Watchdog\Notifications\MembersHaveChanged;

class GroupMembersTest extends DogTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['watchdog.watchdogs' => [WatchGroupMembers::class]]);

        DirectoryEmulator::setup();
    }

    public function test()
    {
        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
            'member'      => ['foo', 'bar'],
        ]);

        $this->artisan('watchdog:monitor');

        $notifiable = app(WatchGroupMembers::class);

        $this->expectsNotification($notifiable, MembersHaveChanged::class);

        $object->update(['member' => ['foo', 'bar', 'baz']]);

        $this->artisan('watchdog:monitor');
    }
}
