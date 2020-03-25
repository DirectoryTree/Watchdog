<?php

namespace DirectoryTree\Watchdog\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MakeWatchdog extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:watchdog';

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
     * {@inheritDoc}
     */
    public function handle()
    {
        if (parent::handle() !== false) {
            $this->call('make:watchdog-notification', [
                '--no-interaction',
                'name' => $this->getNotificationName()
            ]);
        }
    }

    /**
     * Get the notification name.
     *
     * @return string
     */
    protected function getNotificationName()
    {
        return $this->option('notification') ?? Str::studly($this->argument('name')).'Notification';
    }

    /**
     * {@inheritDoc}
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyNotification'],
            [$this->getNotificationName()],
            $stub
        );

        return parent::replaceNamespace($stub, $name);
    }

    /**
     * {@inheritdoc}
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/watchdog.stub';
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Ldap\Watchdog';
    }

    /**
     * {@inheritDoc}
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the watchdog'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getOptions()
    {
        return [
            ['notification', null, InputOption::VALUE_OPTIONAL, 'The name of the Watchdog notification'],
        ];
    }
}
