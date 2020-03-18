<?php

namespace DirectoryTree\Watchdog\Notifiers;

use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\Ldap\Transformers\AttributeTransformer;

class Generator
{
    /**
     * The notifiers to execute.
     *
     * @var string[]
     */
    protected $notifiers = [];

    /**
     * Constructor.
     *
     * @param array $notifiers
     */
    public function __construct($notifiers = [])
    {
        $this->notifiers = (array) $notifiers;
    }

    /**
     * Set the generator notifiers.
     *
     * @param array $notifiers
     *
     * @return $this
     */
    public function setNotifiers($notifiers = [])
    {
        $this->notifiers = $notifiers;

        return $this;
    }

    /**
     * Generate notifications for configured notifiers.
     *
     * @param LdapObject $object
     */
    public function generate(LdapObject $object)
    {
        $before = $this->transform($object->getOriginalValues());
        $after = $this->transform($object->getUpdatedValues());

        collect($this->notifiers)->transform(function ($notifier) use ($object, $before, $after) {
            return app($notifier)
                ->setObject($object)
                ->setBeforeAttributes($before)
                ->setAfterAttributes($after);
        })->filter(function(Notifier $notifier) use ($object) {
            return $notifier->isEnabled() && $notifier->shouldNotify();
        })->each(function (Notifier $notifier) {
            $notifier->notify();
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
