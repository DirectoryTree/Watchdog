<?php

namespace DirectoryTree\Watchdog\Ldap;

class TypeResolver
{
    const TYPE_USER = 'user';
    const TYPE_GROUP = 'group';
    const TYPE_DOMAIN = 'domain';
    const TYPE_COMPUTER = 'computer';
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
        'user'                  => self::TYPE_USER,
        'inetorgperson'         => self::TYPE_USER,
        'group'                 => self::TYPE_GROUP,
        'groupofnames'          => self::TYPE_GROUP,
        'groupofentries'        => self::TYPE_GROUP,
        'groupofuniquenames'    => self::TYPE_GROUP,
        'domain'                => self::TYPE_DOMAIN,
        'computer'              => self::TYPE_COMPUTER,
        'locality'              => self::TYPE_CONTAINER,
        'container'             => self::TYPE_CONTAINER,
        'lostandfound'          => self::TYPE_CONTAINER,
        'organizationalunit'    => self::TYPE_CONTAINER,
        'msds-quotacontainer'   => self::TYPE_CONTAINER,
    ];

    /**
     * Constructor.
     *
     * @param array $classes
     */
    public function __construct(array $classes = [])
    {
        $this->classes = array_reverse(
            array_map('strtolower', $classes)
        );
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
