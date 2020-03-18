<?php

namespace DirectoryTree\Watchdog\Conditions;

interface Condition
{
    public function passes($before, $after);
}
