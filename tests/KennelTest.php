<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Conditions\Condition;

class KennelTest extends TestCase
{
    public function test_notifier_is_enabled_by_default()
    {
        $this->assertTrue((new Watchdog())->enabled());
    }

    public function test_notifier_should_notify_by_default()
    {
        $this->assertTrue((new Watchdog())->shouldSendNotification());
    }

    public function test_notifier_should_check_conditions()
    {
        $notifier = (new Watchdog())->setConditions([
            FailingConditionStub::class,
        ]);
        $this->assertFalse($notifier->shouldSendNotification());

        $notifier->setConditions([
            PassingConditionStub::class,
            FailingConditionStub::class,
        ]);
        $this->assertFalse($notifier->shouldSendNotification());

        $notifier->setConditions([
            PassingConditionStub::class,
            PassingConditionStub::class,
        ]);
        $this->assertTrue($notifier->shouldSendNotification());
    }
}

class PassingConditionStub extends Condition
{
    public function passes()
    {
        return true;
    }
}

class FailingConditionStub extends Condition
{
    public function passes()
    {
        return false;
    }
}
