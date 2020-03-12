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
        $before = (new AttributeTransformer($object->getOriginalValues()));
        $after = (new AttributeTransformer($object->getUpdatedValues()));

        collect($this->notifiers)->transform(function ($notifier) use ($before, $after) {
            return app($notifier)
                ->setBeforeAttributes($before)
                ->setAfterAttributes($after);
        })->filter(function(Notifier $notifier) use ($object) {
            return $notifier->isEnabled() && $notifier->shouldNotify();
        })->each(function (Notifier $notifier) {
            $notifier->notify();
        });
    }
}
