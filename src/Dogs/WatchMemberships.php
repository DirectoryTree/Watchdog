<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\MembersHaveChanged;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\GroupsChanged;

class WatchMemberships extends Watchdog
{
    protected $conditions = [GroupsChanged::class];

    public function added()
    {
        return $this->after->attribute('memberof')->diff(
            $this->before->attribute('memberof')
        );
    }

    public function removed()
    {
        return $this->before->attribute('memberof')->diff(
            $this->after->attribute('memberof')
        );
    }

    public function getName()
    {
        return trans('watchdog::members_changed');
    }

    public function getKey()
    {
        return 'watchdog.accounts.memberships';
    }

    public function notification()
    {
        return MembersHaveChanged::class;
    }
}
