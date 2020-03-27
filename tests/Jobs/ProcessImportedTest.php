<?php

namespace DirectoryTree\Watchdog\Tests\Jobs;

use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\LdapChange;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapScanEntry;
use DirectoryTree\Watchdog\LdapWatcher;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Jobs\ProcessImported;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\PasswordChanged;

class ProcessImportedTest extends TestCase
{
    public function test_scan_entries_are_imported()
    {
        $scan = factory(LdapScan::class)->create();

        factory(LdapScanEntry::class)->times(10)->create([
            'scan_id' => $scan->id,
        ]);

        ProcessImported::dispatch($scan);

        $this->assertCount(10, LdapObject::get());
    }

    public function test_trashed_objects_are_restored_when_scanned()
    {
        $ldap = factory(LdapWatcher::class)->create();

        $scan = factory(LdapScan::class)->create([
            'watcher_id' => $ldap->id,
        ]);

        $object = factory(LdapObject::class)->create([
            'watcher_id' => $ldap->id,
            'deleted_at'    => now(),
        ]);

        factory(LdapScanEntry::class)->create([
            'scan_id' => $scan->id,
            'guid'    => $object->guid,
        ]);

        $this->assertTrue($object->trashed());

        ProcessImported::dispatch($scan);

        $this->assertFalse($object->fresh()->trashed());
        $this->assertEquals(1, LdapObject::count());
    }

    public function test_changes_are_created()
    {
        $ldap = factory(LdapWatcher::class)->create();

        $scan = factory(LdapScan::class)->create([
            'watcher_id' => $ldap->id,
        ]);

        $object = factory(LdapObject::class)->create([
            'watcher_id' => $ldap->id,
            'values'        => [
                'foo' => 'bar',
                'baz' => 'sas',
            ],
        ]);

        factory(LdapScanEntry::class)->create([
            'scan_id' => $scan->id,
            'guid'    => $object->guid,
            'values'  => [
                'foo' => 'baz',
                'baz' => 'sas',
            ],
        ]);

        ProcessImported::dispatch($scan);

        $change = LdapChange::first();

        $this->assertEquals('bar', $change->before);
        $this->assertEquals('baz', $change->after);
        $this->assertEquals(1, LdapChange::count());
    }

    public function test_notifiers_are_executed_when_ldap_objects_are_updated()
    {
        $ldap = factory(LdapWatcher::class)->create();

        config(["watchdog.watch.{$ldap->model}" => [TestPasswordHasChangedStubWatchdog::class]]);

        $scan = factory(LdapScan::class)->create([
            'watcher_id' => $ldap->id,
        ]);

        $object = factory(LdapObject::class)->create([
            'watcher_id' => $ldap->id,
            'values'        => ['pwdlastset' => ['0']],
        ]);

        factory(LdapScanEntry::class)->create([
            'scan_id' => $scan->id,
            'guid'    => $object->guid,
            'values'  => ['pwdlastset' => ['10000']],
        ]);

        $_SERVER['notified'] = false;

        ProcessImported::dispatch($scan);

        $change = LdapChange::first();

        $this->assertTrue($_SERVER['notified']);
        $this->assertEquals(['0'], $change->before);
        $this->assertEquals(['10000'], $change->after);
        $this->assertEquals('pwdlastset', $change->attribute);
    }
}

class TestPasswordHasChangedStubWatchdog extends Watchdog
{
    protected $conditions = [PasswordChanged::class];

    public function notify($instance)
    {
        $_SERVER['notified'] = true;
    }
}
