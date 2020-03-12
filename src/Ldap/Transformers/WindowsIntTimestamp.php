<?php

namespace DirectoryTree\Watchdog\Ldap\Transformers;

class WindowsIntTimestamp extends Timestamp
{
    /**
     * The LDAP timestamp type.
     *
     * @var string
     */
    protected $type = 'windows-int';
}
