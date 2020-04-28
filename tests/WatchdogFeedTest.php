<?php

namespace DirectoryTree\Watchdog\Tests;

use Carbon\Carbon;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapChange;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapWatcher;
use Illuminate\Foundation\Testing\WithFaker;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;

class WatchdogFeedTest extends TestCase
{
    use WithFaker;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ldap.connections', [
            'default' => [
                'base_dn' => 'dc=local,dc=com',
            ],
        ]);

        $model = Entry::class;

        $app['config']->set("watchdog.watch.{$model}", []);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('watchdog:setup');

        DirectoryEmulator::setup();
    }

    public function test_watch_dogs_can_be_fed()
    {
        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
        ]);

        $this->artisan('watchdog:run');

        $scan = LdapScan::first();
        $this->assertEquals(1, $scan->processed);
        $this->assertInstanceOf(Carbon::class, $scan->completed_at);
        $this->assertInstanceOf(LdapWatcher::class, $scan->watcher);
        $this->assertEquals(LdapScan::STATE_PURGED, $scan->progress()->get()->last()->state);

        $imported = LdapObject::first();
        $this->assertEquals($object->cn[0], $imported->values['cn'][0]);
        $this->assertEquals($object->objectclass, $imported->values['objectclass']);
        $this->assertEquals($object->getConvertedGuid(), $imported->values['objectguid'][0]);
    }

    public function test_watch_dogs_detect_changes()
    {
        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
        ]);

        $this->artisan('watchdog:run');

        $this->assertEquals(0, LdapChange::count());

        $object->cn = 'Jane Doe';

        $this->artisan('watchdog:run');
    }
}
