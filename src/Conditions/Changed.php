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
            if ($this->before->attribute($attribute) != $this->after->attribute($attribute)) {
                return true;
            }
        }

        return false;
    }
}
