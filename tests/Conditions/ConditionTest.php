<?php

namespace DirectoryTree\Watchdog\Tests\Conditions;

use DirectoryTree\Watchdog\Conditions\Condition;
use DirectoryTree\Watchdog\Tests\TestCase;

class ConditionTest extends TestCase
{
    public function test_attributes_keys_are_lower_cased()
    {
        $this->assertTrue(
            (new CasingConditionStub(['FOO' => []], ['BAR' => []]))->passes()
        );
    }

    public function test_null_values_are_accepted()
    {
        $this->assertTrue(
            (new NullConditionStub(null, null))->passes()
        );
    }

    public function test_string_values_are_accepted()
    {
        $this->assertTrue(
            (new StringConditionStub('foo', 'bar'))->passes()
        );
    }
}

class CasingConditionStub extends Condition
{
    public function passes()
    {
        return array_key_exists('foo', $this->before) &&
            array_key_exists('bar', $this->after);
    }
}

class NullConditionStub extends Condition
{
    public function passes()
    {
        return is_array($this->before) && is_array($this->after);
    }
}

class StringConditionStub extends Condition
{
    public function passes()
    {
        return $this->before[0] === 'foo' && $this->after[0] === 'bar';
    }
}
