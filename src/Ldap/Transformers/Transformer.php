<?php

namespace DirectoryTree\Watchdog\Ldap\Transformers;

use Illuminate\Support\Arr;

abstract class Transformer
{
    /**
     * The value to transform.
     *
     * @var array
     */
    protected $value;

    /**
     * Constructor.
     *
     * @param array $value
     */
    public function __construct(array $value)
    {
        $this->value = array_change_key_case($value, CASE_LOWER);
    }

    /**
     * Transform the value.
     *
     * @return array
     */
    abstract public function transform();

    /**
     * Get the first value from the value.
     *
     * @return mixed
     */
    protected function getFirstValue()
    {
        return Arr::first($this->value);
    }
}
