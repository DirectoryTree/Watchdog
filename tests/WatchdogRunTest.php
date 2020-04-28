<?php

namespace DirectoryTree\Watchdog\Tests;

use Carbon\Carbon;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapChange;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapWatcher;
use Illuminate\Foundation\Testing\WithFaker;
use DirectoryTree\Watchdog\Ldap\TypeResolver;
use LdapRecord\Models\ActiveDirectory\Entry;
use LdapRecord\Laravel\Testing\DirectoryEmulator;

class WatchdogRunTest extends TestCase
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

    public function test_objects_are_imported()
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

    public function test_nested_objects_are_assigned_proper_hierarchy()
    {
        $root = new Entry([
            'objectguid'  => $this->faker->uuid,
            'objectclass' => [TypeResolver::TYPE_DOMAIN],
        ]);

        $root->setDn('dc=local,dc=com')->save();

        $child = new Entry([
            'cn'          => 'John Doe',
            'objectguid'  => $this->faker->uuid,
            'objectclass' => [TypeResolver::TYPE_USER],
        ]);

        $child->inside($root)->save();

        $this->artisan('watchdog:run');

        $imported = LdapObject::get();
        $rootImported = $imported->first();
        $childImported = $imported->last();

        $this->assertCount(2, $imported);

        $this->assertFalse($rootImported->isChild());
        $this->assertTrue($rootImported->isRoot());

        $this->assertFalse($childImported->isRoot());
        $this->assertTrue($childImported->isChild());

        $this->assertEquals($childImported->parent_id, $rootImported->id);
    }

    public function test_object_changes_are_detected()
    {
        $object = Entry::create([
            'cn'          => 'John Doe',
            'objectclass' => ['foo'],
            'objectguid'  => $this->faker->uuid,
        ]);

        $this->artisan('watchdog:run');

        $this->assertEquals(0, LdapChange::count());

        $object->fill(['cn' => 'Jane Doe'])->save();

        $this->artisan('watchdog:run');

        $this->assertEquals(1, LdapChange::count());

        $change = LdapChange::first();

        $this->assertEquals('cn', $change->attribute);
        $this->assertEquals(['John Doe'], $change->before);
        $this->assertEquals(['Jane Doe'], $change->after);
    }
}
