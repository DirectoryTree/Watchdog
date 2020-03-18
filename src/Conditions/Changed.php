<?php

namespace DirectoryTree\Watchdog\Conditions;

class Changed
{
    /**
     * The attributes to check.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Determine if any of the attributes have changed values.
     *
     * @param array|null $before
     * @param array|null $after
     *
     * @return bool
     */
    public function passes($before, $after)
    {
        foreach ($this->attributes as $attribute) {
            if (
                $this->getAttributeValues($attribute, $before) !=
                $this->getAttributeValues($attribute, $after)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the values of the given attribute.
     *
     * @param string     $attribute
     * @param array|null $attributes
     *
     * @return array
     */
    protected function getAttributeValues($attribute, $attributes)
    {
        $values = $attributes[$attribute] ?? [];
        sort($values);

        return $values;
    }
}
