<?php

namespace DirectoryTree\Watchdog\Notifiers\Conditions;

interface Condition
{
    public function passes($before, $after);
}
