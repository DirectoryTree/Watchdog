<?php

namespace DirectoryTree\Watchdog\Conditions;

class Changed extends Condition
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
     * @return bool
     */
    public function passes()
    {
        foreach ($this->attributes as $attribute) {
            if (
                $this->getAttributeValues($attribute, $this->before) !=
                $this->getAttributeValues($attribute, $this->after)
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
