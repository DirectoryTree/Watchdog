<?php

namespace DirectoryTree\Watchdog\Tests;

class MakeWatchdogTest extends TestCase
{
    public function test_command_works()
    {
        $this->artisan('watchdog:make', ['name' => 'Foo']);

        $this->assertFileExists(app_path('Ldap/Watchdog/Foo.php'));
    }
}
