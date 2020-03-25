<?php

namespace DirectoryTree\Watchdog\Conditions;

use DirectoryTree\Watchdog\State;

abstract class Condition
{
    /**
     * The object value state before the change.
     *
     * @var State
     */
    protected $before;

    /**
     * The object value state after the change.
     *
     * @var State
     */
    protected $after;

    /**
     * Constructor.
     *
     * @param State $before
     * @param State $after
     */
    public function __construct(State $before, State $after)
    {
        $this->before = $before;
        $this->after = $after;
    }

    /**
     * Determine if the condition passes.
     *
     * @return bool
     */
    abstract public function passes();
}
