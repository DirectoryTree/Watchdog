<?php

namespace DirectoryTree\Watchdog\Commands;

use Illuminate\Support\Str;
use LdapRecord\Models\Model;
use Illuminate\Console\Command;
use DirectoryTree\Watchdog\LdapConnection;

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
        $this->info('Starting to setup configured models...');

        collect(config('watchdog.watch', []))->each(function ($watchdogs, $model) {
            tap(new $model(), function (Model $model) {
                $name = $model->getConnectionName() ?? $model::getConnectionContainer()->getDefaultConnectionName();

                $connection = LdapConnection::firstOrNew([
                    'name' => $name,
                ])->fill([
                    'slug'  => Str::slug($name),
                    'model' => get_class($model),
                ]);

                $connection->save();

                if ($connection->wasRecentlyCreated) {
                    $this->info("Successfully setup connection [$name].");
                } else {
                    $this->info("Connection [$name] is already imported.");
                }
            });
        });
    }
}
