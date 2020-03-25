<?php

namespace DirectoryTree\Watchdog\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeWatchdogNotification extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:watchdog-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Watchdog notification.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Watchdog Notification';

    /**
     * {@inheritdoc}
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/notification.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Ldap\Watchdog\Notifications';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the notification'],
        ];
    }
}
