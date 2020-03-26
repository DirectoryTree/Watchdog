<?php

namespace DirectoryTree\Watchdog\Tests\Ldap;

use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Ldap\TypeGuesser;

class TypeGuesserTest extends TestCase
{
    public function test_first_object_class_determines_type()
    {
        $classes = ['foo', 'bar', 'user'];
        $this->assertEquals(TypeGuesser::TYPE_USER, (new TypeGuesser($classes))->get());

        $classes = ['foo', 'USER', 'bar'];
        $this->assertEquals(TypeGuesser::TYPE_USER, (new TypeGuesser($classes))->get());
    }

    public function test_null_is_returned_when_type_cannot_be_guessed()
    {
        $classes = ['foo', 'bar', 'baz'];
        $this->assertNull((new TypeGuesser($classes))->get());
    }
}
