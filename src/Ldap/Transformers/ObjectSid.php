<?php

namespace DirectoryTree\Watchdog\Ldap\Transformers;

use InvalidArgumentException;
use LdapRecord\Models\Attributes\Sid;

class ObjectSid extends Transformer
{
    /**
     * Transform the value.
     *
     * @return array
     */
    public function transform()
    {
        try {
            return [(new Sid($this->getFirstValue()))->getValue()];
        } catch (InvalidArgumentException $e) {
            return $this->value;
        }
    }
}
