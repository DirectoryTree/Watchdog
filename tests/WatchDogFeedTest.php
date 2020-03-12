<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapConnection;
use Illuminate\Foundation\Testing\WithFaker;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;

class WatchDogFeedTest extends TestCase
{
    use WithFaker;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('ldap.connections', [
            'default' => [
                'base_dn' => 'dc=local,dc=com',
            ]
        ]);

        $app['config']->set('watchdog.models', [Entry::class]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('watchdog:setup');
    }

    public function test_watch_dogs_can_be_fed()
    {
        DirectoryEmulator::setup();

        $object = Entry::create([
            'cn' => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid' => $this->faker->uuid,
        ]);

        $this->artisan('watchdog:feed');

        $scan = LdapScan::first();
        $this->assertTrue($scan->success);
        $this->assertEquals(1, $scan->synchronized);
        $this->assertInstanceOf(LdapConnection::class, $scan->ldap);

        $imported = LdapObject::first();
        $this->assertEquals($object->cn[0], $imported->values['cn'][0]);
        $this->assertEquals($object->objectclass, $imported->values['objectclass']);
        $this->assertEquals($object->getConvertedGuid(), $imported->values['objectguid'][0]);
    }
}
