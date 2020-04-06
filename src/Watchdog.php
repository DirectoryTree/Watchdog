<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Support\Str;
use Illuminate\Notifications\RoutesNotifications;
use DirectoryTree\Watchdog\Notifications\ObjectHasChanged;

class Watchdog
{
    use RoutesNotifications;

    /**
     * The LDAP object.
     *
     * @var LdapObject
     */
    protected $object;

    /**
     * The objects state before the change took place.
     *
     * @var State
     */
    protected $before;

    /**
     * The objects state after the change took place.
     *
     * @var State
     */
    protected $after;

    /**
     * The conditions of the watchdog.
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * Set or get the LdapObject.
     *
     * @param LdapObject|null $object
     *
     * @return $this|LdapObject
     */
    public function object(LdapObject $object = null)
    {
        if (is_null($object)) {
            return $this->object;
        }

        $this->object = $object;

        return $this;
    }

    /**
     * Set or get the 'before' state.
     *
     * @param State|null $before
     *
     * @return $this|State
     */
    public function before(State $before = null)
    {
        if (is_null($before)) {
            return $this->before;
        }

        $this->before = $before;

        return $this;
    }

    /**
     * Set or get the 'after' state.
     *
     * @param State|null $after
     *
     * @return $this|State
     */
    public function after(State $after = null)
    {
        if (is_null($after)) {
            return $this->after;
        }

        $this->after = $after;

        return $this;
    }

    /**
     * Get the names of attributes that were modified on the LDAP object.
     *
     * @return array
     */
    public function modified()
    {
        return array_unique(
            array_merge($this->diffAfter(), $this->diffBefore())
        );
    }

    /**
     * Get the names of attributes that have been modified or do not exist in the 'before' state.
     *
     * @return array
     */
    public function diffAfter()
    {
        return array_keys(
            array_diff(
                array_map('serialize', $this->after->attributes()->jsonSerialize()),
                array_map('serialize', $this->before->attributes()->jsonSerialize())
            )
        );
    }

    /**
     *  Get the names of attributes that have been modified or do not exist in the 'after' state.
     *
     * @return array
     */
    public function diffBefore()
    {
        return array_keys(
            array_diff(
                array_map('serialize', $this->before->attributes()->jsonSerialize()),
                array_map('serialize', $this->after->attributes()->jsonSerialize())
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
        $watchdog = get_class($this);

        return trans("watchdog::watchdogs.$watchdog.name") ?? $watchdog;
    }

    /**
     * Get the description of the watchdog.
     *
     * @return string|null
     */
    public function getDescription()
    {
    }

    /**
     * Get the notifiable subject for the watchdog.
     *
     * @return string
     */
    public function getNotifiableSubject()
    {
        $watchdog = get_class($this);

        return trans("watchdog::watchdogs.$watchdog.subject", [
            'object' => $this->object->name,
        ]);
    }

    /**
     * Get the notification key for the watchdog.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getRouteKey();
    }

    /**
     * Get the value of the watchdogs route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return Str::slug(Str::kebab(class_basename($this)));
    }

    /**
     * Generate a watchdog notification for the current channels.
     *
     * @return void
     */
    public function bark()
    {
        rescue(function () {
            $this->createNotificationRecord(
                $sent = $this->sendNotification()
            );
        });
    }

    /**
     * Send a watchdog notification.
     *
     * @return bool
     */
    protected function sendNotification()
    {
        $this->notify(
            app($this->notification())->delay($this->secondsBetweenNotifications())
        );

        return !empty($this->channels());
    }

    /**
     * Get the number of seconds to wait in-between notifications.
     *
     * @return int
     */
    protected function secondsBetweenNotifications()
    {
        $delay = config('watchdog.notifications.seconds_between_notifications', 5);

        if (! is_null($lastNotification = $this->lastNotification())) {
            return $lastNotification->created_at->diffInSeconds(now()) > $delay ? 0 : $delay;
        }

        return $delay;
    }

    /**
     * Create a record indicating a notification has been sent.
     *
     * @param $sent bool
     *
     * @return void
     */
    protected function createNotificationRecord($sent = true)
    {
        $this->object->notifications()->create([
            'sent'          => $sent,
            'data'          => $this->data(),
            'watchdog'      => $this->getKey(),
            'channels'      => $this->channels(),
            'notification'  => $this->notification(),
        ]);
    }

    /**
     * Determine whether the watchdog should fire a notification.
     *
     * @return bool
     */
    public function shouldSendNotification()
    {
        return $this->enabled() && $this->passesAllConditions();
    }

    /**
     * Determine if a notification has already been sent for the current LDAP object.
     *
     * @return bool
     */
    public function notificationHasBeenSent()
    {
        return !is_null($this->lastNotificationForObject());
    }

    /**
     * Get the last notification sent to the object inspected by the watchdog.
     *
     * @return \DirectoryTree\Watchdog\LdapNotification|null
     */
    public function lastNotificationForObject()
    {
        return $this->notifications()
            ->where('object_id', '=', $this->object->id)
            ->first();
    }

    /**
     * Get the last notification sent by the watchdog.
     *
     * @return \DirectoryTree\Watchdog\LdapNotification|null
     */
    public function lastNotification()
    {
        return $this->notifications()->first();
    }

    /**
     * Get a query for the latest watchdog notifications.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function notifications()
    {
        $model = ModelRepository::get(LdapNotification::class);

        return $model::where('watchdog', '=', $this->getKey())->latest();
    }

    /**
     * Determine if all of the watchdogs conditions pass.
     *
     * @return bool
     */
    protected function passesAllConditions()
    {
        return collect($this->conditions)->filter(function ($condition) {
            return $this->makeCondition($condition)->passes();
        })->count() === count($this->conditions);
    }

    /**
     * Create a new instance of the condition.
     *
     * @param string $condition
     *
     * @return \DirectoryTree\Watchdog\Conditions\Condition
     */
    protected function makeCondition($condition)
    {
        return new $condition(
            $this->before ?? new State(),
            $this->after ?? new State()
        );
    }

    /**
     * Determine whether the watchdog is enabled.
     *
     * @return bool
     */
    public function enabled()
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
     * Get the arrayable data of the watchdog.
     *
     * @return array
     */
    public function data()
    {
        return [
            'before'  => $this->before->toJson(),
            'after'   => $this->after->toJson(),
            'extra'   => collect($this->extra())->toJson(),
            'subject' => $this->getNotifiableSubject(),
        ];
    }

    /**
     * Get extra data to insert into the notification record.
     *
     * @return array
     */
    public function extra()
    {
        return [];
    }

    /**
     * Get the notification channels for the watchdog.
     *
     * @return array
     */
    public function channels()
    {
        $model = $this->object->watcher->model;

        $watchdog = get_class($this);

        return config("watchdog.watch.$model.$watchdog", []);
    }

    /**
     * Get the email to send for mail notifications.
     *
     * @return string|null
     */
    public function routeNotificationForMail()
    {
        return config('watchdog.notifications.mail.to');
    }
}
