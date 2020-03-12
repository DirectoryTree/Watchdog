<?php

namespace DirectoryTree\Watchdog\Notifiers\Conditions;

class PasswordChanged extends Changed
{
    protected $attributes = ['pwdlastset'];
}
