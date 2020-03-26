<?php

namespace DirectoryTree\Watchdog;

use Illuminate\Support\Arr;

class State
{
    /**
     * The attributes for the current state.
     *
     * @var \Illuminate\Support\Collection
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

        $this->attributes = collect(
            array_change_key_case(Arr::wrap($attributes), CASE_LOWER)
        );
    }

    /**
     * Get all of attributes from the state in a collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * Get a single attribute value collection from the state.
     *
     * @param string $name
     *
     * @return \Illuminate\Support\Collection
     */
    public function attribute($name)
    {
        $values = Arr::wrap($this->attributes[strtolower($name)] ?? []);

        sort($values);

        return collect($values);
    }
}
