<?php

namespace DirectoryTree\Watchdog\Tests\Ldap;

use DirectoryTree\Watchdog\Tests\TestCase;
use DirectoryTree\Watchdog\Ldap\TypeResolver;

class TypeGuesserTest extends TestCase
{
    public function test_first_object_class_determines_type()
    {
        $classes = ['foo', 'bar', 'user'];
        $this->assertEquals(TypeResolver::TYPE_USER, (new TypeResolver($classes))->get());

        $classes = ['foo', 'USER', 'bar'];
        $this->assertEquals(TypeResolver::TYPE_USER, (new TypeResolver($classes))->get());
    }

    public function test_computer_type_is_determined_from_other_valid_types()
    {
        // The computer class will always be last, as it inherits the user object class.
        $this->assertEquals(TypeResolver::TYPE_COMPUTER, (new TypeResolver([
            'user', 'computer',
        ]))->get());

        $this->assertEquals(TypeResolver::TYPE_USER, (new TypeResolver([
            'computer', 'user',
        ]))->get());
    }

    public function test_null_is_returned_when_type_cannot_be_guessed()
    {
        $classes = ['foo', 'bar', 'baz'];
        $this->assertNull((new TypeResolver($classes))->get());
    }
}
