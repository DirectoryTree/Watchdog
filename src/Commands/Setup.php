<?php

namespace DirectoryTree\Watchdog\Commands;

use Illuminate\Support\Str;
use LdapRecord\Models\Model;
use Illuminate\Console\Command;
use DirectoryTree\Watchdog\LdapWatcher;

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

                $watcher = LdapWatcher::firstOrNew([
                    'model' => get_class($model)
                ]);

                $watcher->fill([
                    'name' => $watcher->name ?? Str::studly($name),
                ]);

                $watcher->save();

                if ($watcher->wasRecentlyCreated) {
                    $this->info("Successfully setup watcher for model [{$watcher->model}].");
                } else {
                    $this->info("Watcher for model [{$watcher->model}] has already been imported.");
                }
            });
        });
    }
}
