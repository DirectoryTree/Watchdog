<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Support\Arr;

class State
{
    /**
     * The attributes for the current state.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Constructor.
     *
     * @param static|array|string|null $attributes
     */
    public function __construct($attributes = [])
    {
        if ($attributes instanceof static) {
            $attributes = $attributes->attributes();
        }

        $this->attributes = array_change_key_case(Arr::wrap($attributes), CASE_LOWER);
    }

    /**
     * Get all attributes from the state.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * Get a single attribute value from the state.
     *
     * @param string $name
     *
     * @return array
     */
    public function attribute($name)
    {
        $values = Arr::wrap($this->attributes[strtolower($name)] ?? []);

        sort($values);

        return $values;
    }
}
