<?php

namespace DirectoryTree\Watchdog\Commands;

use Illuminate\Console\Command;
use DirectoryTree\Watchdog\LdapWatcher;
use DirectoryTree\Watchdog\Jobs\ExecuteScan;
use DirectoryTree\Watchdog\WatcherRepository;

class Monitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watchdog:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitors the configured LDAP models for changes.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('---- Watchdog ----');
        $this->info('Starting to queue monitor...');

        $watchers = WatcherRepository::toMonitor();

        if ($watchers->isEmpty()) {
            return $this->info('No LDAP connections are scheduled to be synchronized.');
        }

        $bar = $this->output->createProgressBar($watchers->count());

        $bar->start();

        $watchers->each(function (LdapWatcher $watcher) use ($bar) {
            ExecuteScan::dispatch($watcher);

            $bar->advance();
        });

        $bar->finish();

        $this->info("\n");
        $this->table(['Domains Queued'], $watchers->map->only('name'));
    }
}
