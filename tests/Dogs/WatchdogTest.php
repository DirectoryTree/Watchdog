<?php

namespace DirectoryTree\Watchdog\Tests\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapNotification;
use Illuminate\Support\Facades\Notification;
use DirectoryTree\Watchdog\Notifications\ObjectHasChanged;

class WatchdogTest extends DogTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set(['watchdog.notifications.seconds_between_notifications' => $delay = 5]);
    }

    public function test_delay_is_added_to_sent_notification()
    {
        Notification::fake();

        $watchdog = tap(new Watchdog, function (Watchdog $watchdog) {
            $watchdog->object(factory(LdapObject::class)->create());

            $watchdog->bark();
        });

        Notification::assertSentTo(
            $watchdog,
            ObjectHasChanged::class,
            function (ObjectHasChanged $notification) {
                return $notification->delay === 5;
            }
        );
    }

    public function test_delay_no_delay_is_added_when_last_notification_that_was_sent_is_older()
    {
        Notification::fake();

        $object = factory(LdapObject::class)->create();

        $watchdog = new Watchdog;
        $watchdog->object($object);

        factory(LdapNotification::class)->create([
            'created_at' => now()->subSeconds(6),
            'object_id' => $object->id,
            'watchdog' => $watchdog->getKey(),
            'notification' => ObjectHasChanged::class,
        ]);

        $watchdog->bark();

        Notification::assertSentTo(
            $watchdog,
            ObjectHasChanged::class,
            function (ObjectHasChanged $notification) {
                return $notification->delay === 0;
            }
        );
    }
}
