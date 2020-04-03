<?php

namespace DirectoryTree\Watchdog\Jobs;

use Illuminate\Pipeline\Pipeline;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\LdapScanEntry;

class ProcessImported extends ScanJob
{
    /**
     * The pipes to run through the pipeline.
     *
     * @var array
     */
    protected $pipes = [
        Pipes\RestoreModelWhenTrashed::class,
        Pipes\AssociateWatcher::class,
        Pipes\AssociateParent::class,
        Pipes\DetectChanges::class,
        Pipes\HydrateProperties::class,
    ];

    /**
     * The total number of processed records.
     *
     * @var int
     */
    protected $processed = 0;

    /**
     * Process the imported scan entries.
     *
     * @return void
     */
    public function handle()
    {
        $this->scan->update(['state' => LdapScan::STATE_PROCESSING]);

        $processed = $this->process($this->scan->rootEntries());

        $this->scan->update([
            'processed' => $this->processed,
            'state'     => LdapScan::STATE_PROCESSED,
        ]);
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
            $object = $this->scan->watcher->objects()->withTrashed()->firstOrNew(['guid' => $entry->guid]);

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

            $this->processed++;

            $this->process($entry->children());
        });
    }
}
