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
        // We cannot determine if the attributes have changed
        // if the objects before state is completely empty.
        // This prevents false positives being generated.
        if ($this->before->attributes()->isEmpty()) {
            return false;
        }

        foreach ($this->attributes as $attribute) {
            if ($this->attributeHasChanged($attribute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect if the attribute has changed by comparing their serialized values.
     *
     * @param string $attribute
     *
     * @return bool
     */
    protected function attributeHasChanged($attribute)
    {
        return $this->before->attribute($attribute)->jsonSerialize() != $this->after->attribute($attribute)->jsonSerialize();
    }
}
