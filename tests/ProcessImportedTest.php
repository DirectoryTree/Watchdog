<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\LdapChange;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapScanEntry;
use DirectoryTree\Watchdog\LdapConnection;
use DirectoryTree\Watchdog\Jobs\ProcessImported;
use DirectoryTree\Watchdog\Notifiers\Conditions\PasswordChanged;
use DirectoryTree\Watchdog\Notifiers\Notifier;

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
        $ldap = factory(LdapConnection::class)->create();

        $scan = factory(LdapScan::class)->create([
            'connection_id' => $ldap->id,
        ]);

        $object = factory(LdapObject::class)->create([
            'connection_id' => $ldap->id,
            'deleted_at' => now(),
        ]);

        factory(LdapScanEntry::class)->create([
            'scan_id' => $scan->id,
            'guid' => $object->guid,
        ]);

        $this->assertTrue($object->trashed());

        ProcessImported::dispatch($scan);

        $this->assertFalse($object->fresh()->trashed());
        $this->assertEquals(1, LdapObject::count());
    }

    public function test_changes_are_created()
    {
        $ldap = factory(LdapConnection::class)->create();

        $scan = factory(LdapScan::class)->create([
            'connection_id' => $ldap->id,
        ]);

        $object = factory(LdapObject::class)->create([
            'connection_id' => $ldap->id,
            'values' => [
                'foo' => 'bar',
                'baz' => 'sas',
            ],
        ]);

        factory(LdapScanEntry::class)->create([
            'scan_id' => $scan->id,
            'guid' => $object->guid,
            'values' => [
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
        config(['watchdog.notifiers' => [TestPasswordHasChangedStubNotifier::class]]);

        $ldap = factory(LdapConnection::class)->create();

        $scan = factory(LdapScan::class)->create([
            'connection_id' => $ldap->id,
        ]);

        $object = factory(LdapObject::class)->create([
            'connection_id' => $ldap->id,
            'values' => ['pwdlastset' => ['0']],
        ]);

        factory(LdapScanEntry::class)->create([
            'scan_id' => $scan->id,
            'guid' => $object->guid,
            'values' => ['pwdlastset' => ['10000']],
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

class TestPasswordHasChangedStubNotifier extends Notifier
{
    protected $conditions = [PasswordChanged::class];

    public function notify()
    {
        $_SERVER['notified'] = true;
    }
}
