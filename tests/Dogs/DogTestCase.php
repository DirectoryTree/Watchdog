<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use LdapRecord\Models\ActiveDirectory\Entry;

class DogTestCase extends TestCase
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

        $app['config']->set('watchdog.frequency', 0);
        $app['config']->set('watchdog.models', [Entry::class]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('watchdog:setup');
    }
}
