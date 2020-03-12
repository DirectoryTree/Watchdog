<?php

namespace DirectoryTree\Watchdog\Notifiers;

class Notifier
{
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
     * The conditions of the notifier.
     *
     * @var array
     */
    protected $conditions = [];

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
     * Set the notifier conditions.
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
     * The name of the notifier.
     *
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }

    /**
     * Determine whether the notifier is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Send the notification.
     *
     * @return void
     */
    public function notify()
    {
        //
    }

    /**
     * Determine whether the notifier should fire if all the conditions pass.
     *
     * @return bool
     */
    public function shouldNotify()
    {
        return collect($this->conditions)->filter(function ($condition) {
            return app($condition)->passes($this->before, $this->after);
        })->count() === count($this->conditions);
    }
}
