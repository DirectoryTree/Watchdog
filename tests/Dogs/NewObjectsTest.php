<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use LdapRecord\Models\ActiveDirectory\Entry;
use DirectoryTree\Watchdog\Dogs\WatchNewObjects;
use DirectoryTree\Watchdog\Notifications\ObjectCreated;

class NewObjectsTest extends DogTestCase
{
    protected $model = Entry::class;

    protected $watchdogs = WatchNewObjects::class;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('watchdog.inspect_new_objects', true);
    }

    public function test_notification_is_sent_when_new_object_is_imported()
    {
        Notification::fake();

        Entry::create([
            'cn'            => 'John Doe',
            'objectclass'   => ['foo'],
            'objectguid'    => $this->faker->uuid,
        ]);

        $this->artisan('watchdog:run');

        Notification::assertSentTo(app(WatchNewObjects::class), ObjectCreated::class);

        $notification = LdapNotification::where([
            'notification' => ObjectCreated::class,
            'channels'     => json_encode(['mail']),
        ])->first();

        $this->assertTrue($notification->sent);
        $this->assertEquals(1, $notification->object_id);
        $this->assertEquals(['mail'], $notification->channels);
        $this->assertEquals(ObjectCreated::class, $notification->notification);
    }

    public function test_notification_is_not_sent_when_object_is_updated()
    {
        $object = Entry::create([
            'cn'            => 'John Doe',
            'objectclass'   => ['foo'],
            'objectguid'    => $this->faker->uuid,
        ]);

        $this->artisan('watchdog:run');

        $object->update(['cn' => 'Jane Doe']);

        Notification::fake();

        $this->artisan('watchdog:run');

        Notification::assertNotSentTo(app(WatchNewObjects::class), ObjectCreated::class);
    }
}
