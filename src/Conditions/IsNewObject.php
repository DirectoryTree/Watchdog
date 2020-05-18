<?php

namespace DirectoryTree\Watchdog\Conditions;

class IsNewObject extends Condition
{
    /**
     * Determine if the condition passes.
     *
     * @return bool
     */
    public function passes()
    {
        return $this->before->attributes()->isEmpty() && $this->after->attributes()->isNotEmpty();
    }
}
