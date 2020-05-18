<?php

return [
    \DirectoryTree\Watchdog\Watchdog::class => [
        'name'    => 'Object Changed',
        'subject' => 'Object [:object] has been changed',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchNewObjects::class => [
        'name'    => 'Object Created',
        'subject' => 'Object [:object] has been created',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchComputerLogons::class => [
        'name'    => 'Computer Logons',
        'subject' => 'Computer [:object] has a new login',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchAccountLockout::class => [
        'name'    => 'Account Locked',
        'subject' => 'Account [:object] has been locked',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchAccountDisable::class => [
        'name'    => 'Accounts Disabled',
        'subject' => 'Account [:object] has been disabled',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchAccountEnable::class => [
        'name'    => 'Accounts Enabled',
        'subject' => 'Account [:object] has been enabled',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchAccountExpiry::class => [
        'name'    => 'Accounts Expired',
        'subject' => 'Account [:object] has expired',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchAccountGroups::class => [
        'name'    => 'Account Memberships Changed',
        'subject' => 'Account [:object] has had their groups changed',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchGroupMembers::class => [
        'name'    => 'Group Members Changed',
        'subject' => 'Group [:object] has had members changed',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchAccountLogons::class => [
        'name'    => 'Account Logons',
        'subject' => 'Account [:object] has been logged into',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchPasswordChanges::class => [
        'name'    => 'Passwords Changed',
        'subject' => 'Account [:object] has had their password changed',
    ],
    \DirectoryTree\Watchdog\Dogs\WatchPasswordExpiry::class => [
        'name'    => 'Password Expired',
        'subject' => 'Account [:object] password has expired',
    ],
];
