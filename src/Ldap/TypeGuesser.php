<?php

namespace DirectoryTree\Watchdog\Ldap;

class TypeGuesser
{
    const TYPE_USER = 'user';
    const TYPE_GROUP = 'group';
    const TYPE_DOMAIN = 'domain';
    const TYPE_CONTAINER = 'container';

    /**
     * The LDAP entry's object classes.
     *
     * @var array
     */
    protected $classes = [];

    /**
     * The objectclass type map.
     *
     * @var array
     */
    protected $map = [
        'user'                  => TypeGuesser::TYPE_USER,
        'inetorgperson'         => TypeGuesser::TYPE_USER,
        'group'                 => TypeGuesser::TYPE_GROUP,
        'groupofnames'          => TypeGuesser::TYPE_GROUP,
        'groupofentries'        => TypeGuesser::TYPE_GROUP,
        'groupofuniquenames'    => TypeGuesser::TYPE_GROUP,
        'domain'                => TypeGuesser::TYPE_DOMAIN,
        'locality'              => TypeGuesser::TYPE_CONTAINER,
        'container'             => TypeGuesser::TYPE_CONTAINER,
        'lostandfound'          => TypeGuesser::TYPE_CONTAINER,
        'organizationalunit'    => TypeGuesser::TYPE_CONTAINER,
        'msds-quotacontainer'   => TypeGuesser::TYPE_CONTAINER,
    ];

    /**
     * Constructor.
     *
     * @param array $classes
     */
    public function __construct(array $classes = [])
    {
        $this->classes = array_map('strtolower', $classes);
    }

    /**
     * Determine the LDAP objects type.
     *
     * @return string|null
     */
    public function get()
    {
        foreach ($this->classes as $class) {
            if (array_key_exists($class, $this->map)) {
                return $this->map[$class];
            }
        }
    }
}
