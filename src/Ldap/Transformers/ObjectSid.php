<?php

namespace DirectoryTree\Watchdog\Ldap\Transformers;

use LdapRecord\Utilities;

class ObjectSid extends Transformer
{
    /**
     * Transform the value.
     *
     * @return array
     */
    public function transform()
    {
        if ($value = $this->getFirstValue()) {
            $value = Utilities::isValidSid($value) ? $value : Utilities::binarySidToString($value);
        }

        return $value ? [$value] : $this->value;
    }
}
