<?php

namespace DirectoryTree\Watchdog;

use DirectoryTree\Watchdog\Notifications\Notifiable;
use DirectoryTree\Watchdog\Notifications\ObjectHasChanged;

class Watchdog
{
    /**
     * The LDAP object.
     *
     * @var LdapObject
     */
    protected $object;

    /**
     * The objects values before the change took place.
     *
     * @var array|null
     */
    protected $before;

    /**
     * The objects values after the change took place.
     *
     * @var array|null
     */
    protected $after;

    /**
     * The conditions of the watcher.
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * Set the LDAP object.
     *
     * @param LdapObject $object
     *
     * @return $this
     */
    public function setObject(LdapObject $object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Set the objects 'before' attributes.
     *
     * @param array|null $before
     *
     * @return $this
     */
    public function setBeforeAttributes($before)
    {
        $this->before = $before;

        return $this;
    }

    /**
     * Set the objects 'after' attributes.
     *
     * @param array|null $after
     *
     * @return $this
     */
    public function setAfterAttributes($after)
    {
        $this->after = $after;

        return $this;
    }

    /**
     * Set the watchdog conditions.
     *
     * @param array $conditions
     *
     * @return $this
     */
    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * The name of the watchdog.
     *
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }

    /**
     * Determine whether the watchdog is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Send a notification.
     *
     * @param LdapObject $object
     *
     * @return void
     */
    public function notify(LdapObject $object)
    {
        $this->notifiable()->notify(
            $this->notification($object)
        );
    }

    /**
     * Create a new notification for the watchdog.
     *
     * @param LdapObject $object
     *
     * @return \DirectoryTree\Watchdog\Notifications\BaseNotification
     */
    public function notification(LdapObject $object)
    {
        return new ObjectHasChanged($object);
    }

    /**
     * Determine whether the watchdog should fire a notification.
     *
     * @return bool
     */
    public function shouldNotify()
    {
        return collect($this->conditions)->filter(function ($condition) {
            return (new $condition($this->before, $this->after))->passes();
        })->count() === count($this->conditions);
    }

    /**
     * Create a new notifiable instance.
     *
     * @return Notifiable
     */
    public function notifiable()
    {
        return app(
            config('watchdog.notifications.notifiable', Notifiable::class)
        );
    }
}
