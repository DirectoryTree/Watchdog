<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Notifications\Notifiable;
use DirectoryTree\Watchdog\Notifications\ObjectHasChanged;

class Watchdog
{
    use Notifiable;

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
     * Get the LDAP object.
     *
     * @return LdapObject
     */
    public function getObject()
    {
        return $this->object;
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
     * Get the objects 'before' attributes.
     *
     * @return array|null
     */
    public function getBeforeAttributes()
    {
        return $this->before;
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
     * Get the objects 'after' attributes.
     *
     * @return array|null
     */
    public function getAfterAttributes()
    {
        return $this->after;
    }

    /**
     * Get the attribute names that were modified on the LDAP object.
     *
     * @return array
     */
    public function getModifiedAttributes()
    {
        return array_keys(
            array_diff(
                array_map('serialize', $this->getAfterAttributes()),
                array_map('serialize', $this->getBeforeAttributes())
            )
        );
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
     * Get the conditions for the watchdog.
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Get the name of the watchdog.
     *
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }

    /**
     * Get the notification key for the watchdog.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getName();
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
     * Determine whether the watchdog is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Get the notification for the watchdog.
     *
     * @return string
     */
    public function notification()
    {
        return ObjectHasChanged::class;
    }

    /**
     * @return string|null
     */
    public function routeNotificationForMail()
    {
        return config('watchdog.notifications.mail.to');
    }

    /**
     * @return string|null
     */
    public function routeNotificationForSlack()
    {
        return config('watchdog.notifications.slack.webhook_url');
    }
}
