<?php

namespace DirectoryTree\Watchdog\Commands;

use DirectoryTree\Watchdog\ConnectionRepository;
use DirectoryTree\Watchdog\LdapConnection;
use DirectoryTree\Watchdog\Jobs\ScanConnection;
use Illuminate\Console\Command;

class Feed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watchdog:feed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports the configured LDAP domain objects.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("---- Watchdog ----");
        $this->info("Starting to queue LDAP connection synchronization...");

        $connections = ConnectionRepository::toMonitor();

        if ($connections->isEmpty()) {
            return $this->info('No LDAP connections are scheduled to be synchronized.');
        }

        $bar = $this->output->createProgressBar($connections->count());

        $bar->start();

        $connections->each(function (LdapConnection $connection) use ($bar) {
            ScanConnection::dispatch($connection);

            $bar->advance();
        });

        $bar->finish();

        $this->info("\n");
        $this->table(['Domains Queued'], $connections->map->only('name'));
    }
}
