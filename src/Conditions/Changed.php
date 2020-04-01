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
        // We'll compare attribute values by converting them to a serialized
        // array, so objects are converted to their serialized value and a
        // proper comparison can be performed. Otherwise, exceptions
        // can be thrown and inconsistencies may occur.
        return $this->before->attribute($attribute)->jsonSerialize() != $this->after->attribute($attribute)->jsonSerialize();
    }
}
