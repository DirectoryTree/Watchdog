<?php

namespace DirectoryTree\Watchdog;

use DirectoryTree\Watchdog\Ldap\Transformers\AttributeTransformer;

class Kennel
{
    /**
     * The notifiers to execute.
     *
     * @var string[]
     */
    protected $watchers = [];

    /**
     * Constructor.
     *
     * @param string|array $notifiers
     */
    public function __construct($notifiers = [])
    {
        $this->watchers = (array) $notifiers;
    }

    /**
     * Set the generator notifiers.
     *
     * @param string|array $watchers
     *
     * @return $this
     */
    public function setWatchers($watchers = [])
    {
        $this->watchers = (array) $watchers;

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

        collect($this->watchers)->transform(function ($watchdog) use ($object, $before, $after) {
            return app($watchdog)
                ->setObject($object)
                ->setBeforeAttributes($before)
                ->setAfterAttributes($after);
        })->filter(function(Watchdog $watchdog) {
            return $watchdog->isEnabled() && $watchdog->shouldNotify();
        })->each(function (Watchdog $notifier) use ($object) {
            $notifier->notify($object);
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
        return (new AttributeTransformer($attributes))->transform();
    }
}
