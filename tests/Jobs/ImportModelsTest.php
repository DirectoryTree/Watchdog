<?php

namespace DirectoryTree\Watchdog\Tests\Jobs;

use Carbon\Carbon;
use DirectoryTree\Watchdog\LdapScan;
use LdapRecord\Models\Entry;
use DirectoryTree\Watchdog\LdapWatcher;
use DirectoryTree\Watchdog\LdapScanEntry;
use DirectoryTree\Watchdog\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use DirectoryTree\Watchdog\Jobs\ImportModels;
use LdapRecord\Laravel\Testing\DirectoryEmulator;

class ImportModelsTest extends TestCase
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
    }

    protected function setUp(): void
    {
        parent::setUp();

        DirectoryEmulator::setup('default');
    }

    public function test_scan_is_updated_with_no_models_to_import()
    {
        $watcher = factory(LdapWatcher::class)->create([
            'model' => Entry::class,
        ]);

        $scan = $watcher->scans()->create();

        ImportModels::dispatch($scan);

        $scan->refresh();

        $this->assertEquals(0, $scan->processed);
        $this->assertEquals(0, $scan->imported);
        $this->assertInstanceOf(Carbon::class, $scan->started_at);
        $this->assertEquals(LdapScan::STATE_IMPORTED, $scan->progress()->get()->last()->state);
    }

    public function test_models_can_be_imported()
    {
        foreach (range(1, 10) as $iterator) {
            Entry::create([
                'objectclass' => ['foo', 'bar'],
                'objectguid'  => $this->faker->uuid,
                'cn'          => [$this->faker->name],
            ]);
        }

        $watcher = factory(LdapWatcher::class)->create([
            'model' => Entry::class,
        ]);

        $scan = $watcher->scans()->create();

        ImportModels::dispatch($scan);

        $scan->refresh();

        $this->assertEquals(10, $scan->imported);
        $this->assertEquals(10, LdapScanEntry::count());
        $this->assertInstanceOf(Carbon::class, $scan->started_at);
        $this->assertEquals(LdapScan::STATE_IMPORTED, $scan->progress()->get()->last()->state);
    }
}
