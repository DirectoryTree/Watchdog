<?php

namespace DirectoryTree\Watchdog\Ldap\Transformers;

class LdapTimestamp extends Timestamp
{
    /**
     * The LDAP timestamp type.
     *
     * @var string
     */
    protected $type = 'ldap';
}
