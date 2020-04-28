<?php

namespace DirectoryTree\Watchdog\Commands;

use Illuminate\Console\Command;
use DirectoryTree\Watchdog\LdapWatcher;
use DirectoryTree\Watchdog\Jobs\ExecuteScan;
use DirectoryTree\Watchdog\WatcherRepository;

class Run extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watchdog:run {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitors the setup watchers for changes.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('---- Watchdog ----');
        $this->info('Starting to queue watchers...');

        $watchers = $this->hasOption('force')
            ? WatcherRepository::all()
            : WatcherRepository::toMonitor();

        if ($watchers->isEmpty()) {
            return $this->info('There are no scheduled watchers to be monitored.');
        }

        $bar = $this->output->createProgressBar($watchers->count());

        $bar->start();

        $watchers->each(function (LdapWatcher $watcher) use ($bar) {
            ExecuteScan::dispatch($watcher);

            $bar->advance();
        });

        $bar->finish();

        $this->info("\n");
        $this->table(['Watchers Queued'], $watchers->map->only('name'));
    }
}
