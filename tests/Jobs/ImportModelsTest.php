<?php

namespace DirectoryTree\Watchdog\Tests\Jobs;

use Carbon\Carbon;
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
        $connection = factory(LdapWatcher::class)->create([
            'model' => Entry::class,
        ]);

        $scan = $connection->scans()->create();

        ImportModels::dispatch($scan);

        $scan->refresh();

        $this->assertTrue($scan->success);
        $this->assertEquals(0, $scan->synchronized);
        $this->assertInstanceOf(Carbon::class, $scan->started_at);
        $this->assertInstanceOf(Carbon::class, $scan->completed_at);
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

        $connection = factory(LdapWatcher::class)->create([
            'model' => Entry::class,
        ]);

        $scan = $connection->scans()->create();

        ImportModels::dispatch($scan);

        $scan->refresh();

        $this->assertTrue($scan->success);
        $this->assertEquals(10, $scan->synchronized);
        $this->assertEquals(10, LdapScanEntry::count());
        $this->assertInstanceOf(Carbon::class, $scan->started_at);
        $this->assertInstanceOf(Carbon::class, $scan->completed_at);
    }
}
