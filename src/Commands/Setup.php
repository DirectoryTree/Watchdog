<?php

namespace DirectoryTree\Watchdog\Commands;

use Illuminate\Support\Str;
use LdapRecord\Models\Model;
use Illuminate\Console\Command;
use DirectoryTree\Watchdog\LdapWatcher;
use DirectoryTree\Watchdog\ModelRepository;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watchdog:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the configured LDAP models for monitoring.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('---- Watchdog ----');
        $this->info('Starting to setup configured model watchers...');

        collect(config('watchdog.watch', []))->each(function ($watchdogs, $model) {
            tap(new $model(), function (Model $model) {
                $name = $model->getConnectionName() ?? $model::getConnectionContainer()->getDefaultConnectionName();

                $watcher = $this->firstOrNewWatcher($model)->fill([
                    'name' => $watcher->name ?? Str::studly($name),
                ]);

                $watcher->save();

                if ($watcher->wasRecentlyCreated) {
                    $this->info("Successfully setup watcher for model [{$watcher->model}].");
                } else {
                    $this->info("Watcher for model [{$watcher->model}] has already been setup.");
                }
            });
        });
    }

    /**
     * Get the first watcher that uses the given model, or create a new instance.
     *
     * @param string $ldapModel
     *
     * @return LdapWatcher
     */
    protected function firstOrNewWatcher($ldapModel)
    {
        $model = ModelRepository::get(LdapWatcher::class);

        return $model::firstOrNew(['model' => get_class($ldapModel)]);
    }
}
