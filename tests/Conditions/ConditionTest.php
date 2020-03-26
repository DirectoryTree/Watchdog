<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\Conditions\Condition;
use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\State;

class ConditionTest extends TestCase
{
    public function test_attributes_keys_are_lower_cased()
    {
        $this->assertTrue(
            (new CasingConditionStub(new State(['FOO' => []]), new State(['BAR' => []])))->passes()
        );
    }

    public function test_null_values_are_accepted()
    {
        $this->assertTrue(
            (new NullConditionStub(new State(null), new State(null)))->passes()
        );
    }

    public function test_string_values_are_accepted()
    {
        $this->assertTrue(
            (new StringConditionStub(new State('foo'), new State('bar')))->passes()
        );
    }
}

class CasingConditionStub extends Condition
{
    public function passes()
    {
        return
            $this->before->attributes()->has('foo') &&
            $this->after->attributes()->has('bar');
    }
}

class NullConditionStub extends Condition
{
    public function passes()
    {
        return $this->before->attributes()->isEmpty() && $this->after->attributes()->isEmpty();
    }
}

class StringConditionStub extends Condition
{
    public function passes()
    {
        return $this->before->attributes()[0] === 'foo' && $this->after->attributes()[0] === 'bar';
    }
}
