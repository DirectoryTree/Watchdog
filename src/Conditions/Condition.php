<?php

namespace DirectoryTree\Watchdog\Conditions;

abstract class Condition
{
    /**
     * The attribute value before the change.
     *
     * @var array
     */
    protected $before;

    /**
     * The attribute value after the change.
     *
     * @var array
     */
    protected $after;

    /**
     * Constructor.
     *
     * @param null|string|array $before
     * @param null|string|array $after
     */
    public function __construct($before = [], $after = [])
    {
        $this->before = array_change_key_case((array) $before ?? [], CASE_LOWER);
        $this->after = array_change_key_case((array) $after ?? [], CASE_LOWER);
    }

    /**
     * Determine if the condition passes.
     *
     * @return bool
     */
    abstract public function passes();
}
