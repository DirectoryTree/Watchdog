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

    public function test_computer_type_is_determined_from_other_valid_types()
    {
        // The computer class will always be last, as it inherits the user object class.
        $this->assertEquals(TypeGuesser::TYPE_COMPUTER, (new TypeGuesser([
            'user', 'computer',
        ]))->get());

        $this->assertEquals(TypeGuesser::TYPE_USER, (new TypeGuesser([
            'computer', 'user',
        ]))->get());
    }

    public function test_null_is_returned_when_type_cannot_be_guessed()
    {
        $classes = ['foo', 'bar', 'baz'];
        $this->assertNull((new TypeGuesser($classes))->get());
    }
}
