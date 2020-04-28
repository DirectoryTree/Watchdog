<?php

namespace DirectoryTree\Watchdog\Ldap\Transformers;

use InvalidArgumentException;
use LdapRecord\Models\Attributes\Guid;

class ObjectGuid extends Transformer
{
    /**
     * Transform the value.
     *
     * @return array
     */
    public function transform()
    {
        try {
            return [(new Guid($this->getFirstValue()))->getValue()];
        } catch (InvalidArgumentException $e) {
            return $this->value;
        }
    }
}
