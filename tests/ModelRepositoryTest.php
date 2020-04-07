<?php

namespace DirectoryTree\Watchdog\Tests;

use Exception;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapChange;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapWatcher;
use Illuminate\Database\Eloquent\Model;
use DirectoryTree\Watchdog\LdapScanEntry;
use DirectoryTree\Watchdog\ModelRepository;
use DirectoryTree\Watchdog\LdapNotification;
use DirectoryTree\Watchdog\LdapScanProgress;

class ModelRepositoryTest extends TestCase
{
    public function test_exception_is_thrown_with_invalid_model()
    {
        $this->expectException(Exception::class);

        ModelRepository::swap('invalid', Model::class);
    }

    public function test_model_can_be_swapped()
    {
        ModelRepository::swap(LdapScan::class, Model::class);

        $this->assertEquals(Model::class, ModelRepository::get(LdapScan::class));

        ModelRepository::swap(LdapScan::class, LdapScan::class);

        $this->assertEquals(LdapScan::class, ModelRepository::get(LdapScan::class));
    }

    public function test_get_all_models()
    {
        $this->assertEquals([
            LdapScan::class         => LdapScan::class,
            LdapObject::class       => LdapObject::class,
            LdapChange::class       => LdapChange::class,
            LdapWatcher::class      => LdapWatcher::class,
            LdapScanEntry::class    => LdapScanEntry::class,
            LdapScanProgress::class => LdapScanProgress::class,
            LdapNotification::class => LdapNotification::class,
        ], ModelRepository::all());

        ModelRepository::swap(LdapScan::class, Model::class);

        $this->assertEquals(Model::class, ModelRepository::all()[LdapScan::class]);

        // Swap the model back for the rest of the tests.
        ModelRepository::swap(LdapScan::class, LdapScan::class);
    }
}
