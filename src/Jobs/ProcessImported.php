<?php

namespace DirectoryTree\Watchdog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Pipeline\Pipeline;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapObject;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use DirectoryTree\Watchdog\LdapScanEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessImported implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The LDAP scan being processed.
     *
     * @var LdapScan
     */
    protected $scan;

    /**
     * The pipes to run through the pipeline.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\RestoreModelWhenTrashed::class,
        Pipes\AssociateConnection::class,
        Pipes\AssociateParent::class,
        Pipes\DetectChanges::class,
        Pipes\HydrateProperties::class,
    ];

    /**
     * Constructor.
     *
     * @param LdapScan $scan
     */
    public function __construct(LdapScan $scan)
    {
        $this->scan = $scan;
    }

    /**
     * Process the imported scan entries.
     *
     * @return void
     */
    public function handle()
    {
        $this->process($this->scan->rootEntries());
    }

    /**
     * Synchronize the entries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    protected function process($query)
    {
        $query->cursor()->each(function (LdapScanEntry $entry) {
            /** @var LdapObject $object */
            $object = $this->scan->ldap->objects()->withTrashed()->firstOrNew(['guid' => $entry->guid]);

            // We will go through our process pipes an construct a
            // new instance so they can be used in the pipeline.
            $pipes = collect($this->pipes)->transform(function ($pipe) use ($entry) {
                return new $pipe($this->scan, $entry);
            })->toArray();

            // Here we will create a new pipeline and pipe the
            // database model through our pipes to assemble
            // and perform operations upon it, and then
            // finally saving the assembled model.
            app(Pipeline::class)
                ->send($object)
                ->through($pipes)
                ->then(function (LdapObject $object) {
                    $object->save();
                });

            // We will mark the scanned entry as processed so
            // it is not re-processed again in the event of
            // an exception being generated during.
            $entry->update(['processed' => true]);

            $this->process($entry->children());
        });
    }
}
