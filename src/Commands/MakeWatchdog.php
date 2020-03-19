<?php

namespace DirectoryTree\Watchdog\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeWatchdog extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'watchdog:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Watchdog.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Watchdog';

    /**
     * {@inheritdoc}
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/watchdog.stub';
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
        return $rootNamespace.'\Ldap\Watchdog';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the watchdog'],
        ];
    }
}
