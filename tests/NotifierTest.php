<?php

namespace DirectoryTree\Watchdog\Tests;

use DirectoryTree\Watchdog\Notifiers\Conditions\AccountIsDisabled;
use DirectoryTree\Watchdog\Notifiers\Conditions\MembersChanged;
use DirectoryTree\Watchdog\Notifiers\Conditions\PasswordChanged;
use DirectoryTree\Watchdog\Notifiers\Notifier;
use DirectoryTree\Watchdog\Notifiers\Conditions\Condition;
use DirectoryTree\Watchdog\Notifiers\Conditions\GroupsChanged;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use LdapRecord\Models\Attributes\AccountControl;

class NotifierTest extends TestCase
{
    use DatabaseMigrations;

    public function test_notifier_is_enabled_by_default()
    {
        $this->assertTrue((new Notifier)->isEnabled());
    }

    public function test_notifier_should_notify_by_default()
    {
        $this->assertTrue((new Notifier)->shouldNotify());
    }

    public function test_notifier_should_check_conditions()
    {
        $notifier = (new Notifier)->setConditions([
            FailingConditionStub::class
        ]);
        $this->assertFalse($notifier->shouldNotify());

        $notifier->setConditions([
            PassingConditionStub::class,
            FailingConditionStub::class,
        ]);
        $this->assertFalse($notifier->shouldNotify());

        $notifier->setConditions([
            PassingConditionStub::class,
            PassingConditionStub::class,
        ]);
        $this->assertTrue($notifier->shouldNotify());
    }

    public function test_groups_changed_condition()
    {
        $condition = new GroupsChanged();

        $this->assertFalse($condition->passes(null, null));
        $this->assertFalse($condition->passes([], []));
        $this->assertFalse($condition->passes(['foo'], ['bar']));
        $this->assertFalse($condition->passes(['memberof' => []], ['memberof' => []]));
        $this->assertFalse($condition->passes(['memberof' => ['foo']], ['memberof' => ['foo']]));
        $this->assertFalse($condition->passes(['memberof' => ['bar', 'foo']], ['memberof' => ['foo', 'bar']]));

        $this->assertTrue($condition->passes(['memberof' => ['foo']], ['memberof' => ['bar']]));
        $this->assertTrue($condition->passes(['memberof' => ['foo', 'bar']], ['memberof' => ['bar']]));
        $this->assertTrue($condition->passes(['memberof' => ['foo']], ['memberof' => [null]]));
    }

    public function test_members_changed_condition()
    {
        $condition = new MembersChanged();

        $this->assertFalse($condition->passes(null, null));
        $this->assertFalse($condition->passes([], []));
        $this->assertFalse($condition->passes(['foo'], ['bar']));
        $this->assertFalse($condition->passes(['member' => []], ['member' => []]));
        $this->assertFalse($condition->passes(['member' => ['foo']], ['member' => ['foo']]));
        $this->assertFalse($condition->passes(['member' => ['bar', 'foo']], ['member' => ['foo', 'bar']]));

        $this->assertTrue($condition->passes(['member' => ['foo']], ['member' => ['bar']]));
        $this->assertTrue($condition->passes(['member' => ['foo', 'bar']], ['member' => ['bar']]));
        $this->assertTrue($condition->passes(['member' => ['foo']], ['member' => [null]]));
    }

    public function test_account_is_disabled_condition()
    {
        $condition = new AccountIsDisabled();

        $this->assertFalse($condition->passes(null, null));
        $this->assertFalse($condition->passes([], []));
        $this->assertFalse($condition->passes([0], [0]));

        $this->assertTrue($condition->passes(
            ['userAccountControl' => [0]], ['userAccountControl' => [AccountControl::ACCOUNTDISABLE]]
        ));
    }

    public function test_password_changed()
    {
        $condition = new PasswordChanged();

        $this->assertFalse($condition->passes([], []));
        $this->assertFalse($condition->passes(['pwdlastset' => []], ['pwdlastset' => []]));

        $this->assertTrue($condition->passes(['pwdlastset' => ['0']], ['pwdlastset' => ['10000']]));
    }
}

class PassingConditionStub implements Condition
{
    public function passes($before, $after)
    {
        return true;
    }
}

class FailingConditionStub implements Condition
{
    public function passes($before, $after)
    {
        return false;
    }
}
