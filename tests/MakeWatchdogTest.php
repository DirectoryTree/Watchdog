<?php

namespace DirectoryTree\Watchdog\Tests;

use Illuminate\Support\Facades\File;

class MakeWatchdogTest extends TestCase
{
    public function test_command_generates_watchdog()
    {
        $watchdog = app_path('Ldap/Watchdog/Foo.php');
        $notification = app_path('Ldap/Watchdog/Notifications/FooNotification.php');

        File::delete([$watchdog, $notification]);

        $this->artisan('make:watchdog', ['name' => 'Foo']);

        $this->assertFileExists($watchdog);
        $this->assertFileExists($notification);
    }

    public function test_command_generates_watchdog_with_notification()
    {
        $watchdog = app_path('Ldap/Watchdog/Foo.php');
        $notification = app_path('Ldap/Watchdog/Notifications/Bar.php');

        File::delete([$watchdog, $notification]);

        $this->artisan('make:watchdog', ['name' => 'Foo', '--notification' => 'Bar']);

        $this->assertFileExists($watchdog);
        $this->assertFileExists($notification);
    }
}
