<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\MembersChanged;
use DirectoryTree\Watchdog\Notifications\MembersHaveChanged;
use DirectoryTree\Watchdog\Watchdog;

class WatchGroupMembers extends Watchdog
{
    protected $conditions = [MembersChanged::class];

    public function added()
    {
        return $this->after->attribute('member')->diff(
            $this->before->attribute('member')
        );
    }

    public function removed()
    {
        return $this->before->attribute('member')->diff(
            $this->after->attribute('member')
        );
    }

    public function notification()
    {
        return MembersHaveChanged::class;
    }
}
