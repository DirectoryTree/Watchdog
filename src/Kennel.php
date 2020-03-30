<?php

namespace DirectoryTree\Watchdog;

use DirectoryTree\Watchdog\Ldap\Transformers\AttributeTransformer;

class Kennel
{
    /**
     * The watchdogs to execute.
     *
     * @var string[]
     */
    protected $watchdogs = [];

    /**
     * Constructor.
     *
     * @param string|array $watchdogs
     */
    public function __construct($watchdogs = [])
    {
        $this->watchdogs = (array) $watchdogs;
    }

    /**
     * Set the watchdogs that should be executed.
     *
     * @param string|array $watchdogs
     *
     * @return $this
     */
    public function setWatchdogs($watchdogs = [])
    {
        $this->watchdogs = (array) $watchdogs;

        return $this;
    }

    /**
     * Inspect the object for changes.
     *
     * @param LdapObject $object
     */
    public function inspect(LdapObject $object)
    {
        $before = $this->transform($object->getOriginalValues());
        $after = $this->transform($object->getUpdatedValues());

        collect($this->watchdogs)->transform(function ($channels, $watchdog) use ($object, $before, $after) {
            return app($watchdog)
                ->object($object)
                ->before(new State($before))
                ->after(new State($after));
        })->filter(function (Watchdog $watchdog) {
            return $watchdog->shouldSendNotification();
        })->each(function (Watchdog $watchdog) {
            $watchdog->bark();
        });
    }

    /**
     * Transform the given attributes.
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function transform(array $attributes)
    {
        return (new AttributeTransformer(
            array_change_key_case($attributes, CASE_LOWER)
        ))->transform();
    }
}
