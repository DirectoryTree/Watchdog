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
            if ($this->attributeHasChanged($attribute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect if the attribute has changed.
     *
     * @param string $attribute
     *
     * @return bool
     */
    protected function attributeHasChanged($attribute)
    {
        return $this->before->attribute($attribute)->toArray() != $this->after->attribute($attribute)->toArray();
    }
}
