<?php

namespace DirectoryTree\Watchdog\Jobs;

use Exception;
use Carbon\Carbon;
use LdapRecord\Models\Model;
use Illuminate\Support\Facades\DB;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapScanEntry;
use DirectoryTree\Watchdog\Ldap\TypeGuesser;
use LdapRecord\Models\Types\ActiveDirectory;

class ImportModels extends ScanJob
{
    /**
     * The guids of the LDAP objects scanned.
     *
     * @var array
     */
    protected $guids = [];

    /**
     * Import all of the scanned LDAP objects.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        $this->scan->update([
            'state'      => LdapScan::STATE_IMPORTING,
            'started_at' => now(),
        ]);

        info("Starting to scan domain [{$this->scan->watcher->name}]");

        // We'll initialize a database transaction so all of our
        // inserts and updates are pushed at once. Otherwise
        // each update or insert would be done separately,
        // becoming very resource intensive.
        DB::transaction(function () {
            $this->import($this->createModel());
        });

        $imported = count($this->guids);

        // Upon successful completion, we'll update our scan
        // stats to ensure it is not processed again.
        $this->scan->fill([
            'imported' => $imported,
            'state'     => LdapScan::STATE_IMPORTED,
        ])->save();

        info("Successfully completed scan. Imported [$imported] record(s).");
    }

    /**
     * Create the LDAP model.
     *
     * @return \LdapRecord\Models\Model
     */
    protected function createModel()
    {
        $model = $this->scan->watcher->model;

        if (!class_exists($model)) {
            throw new Exception("No model is defined for domain [$model].");
        }

        $class = '\\'.ltrim($model, '\\');

        return new $class();
    }

    /**
     * Import the LDAP objects on the given connection.
     *
     * @param Model              $model
     * @param LdapScanEntry|null $parent
     *
     * @return void
     */
    protected function import(Model $model, LdapScanEntry $parent = null)
    {
        $this->query($model)
            ->reject(function (Model $object) {
                return empty($object->getConvertedGuid());
            })->each(function (Model $object) use ($model, $parent) {
                $values = $object->jsonSerialize();
                ksort($values);

                $type = $this->getObjectType($object);
                $updated = $this->getObjectUpdatedAt($object);

                /** @var LdapScanEntry $entry */
                $entry = $this->scan->entries()->make();

                $entry->parent()->associate(optional($parent)->id);
                $entry->dn = $object->getDn();
                $entry->name = $object->getName();
                $entry->guid = $object->getConvertedGuid();
                $entry->type = $type;
                $entry->values = $values;
                $entry->ldap_updated_at = $updated;

                $entry->save();

                $this->guids[] = $object->getConvertedGuid();

                // If the object is a container, we will
                // recursively import its descendants.
                if ($type == TypeGuesser::TYPE_CONTAINER) {
                    $this->import($object, $entry);
                }
            });
    }

    /**
     * Queries the LDAP directory.
     *
     * @param Model $model
     *
     * @return \LdapRecord\Query\Collection
     */
    protected function query(Model $model)
    {
        $query = $model->newQuery();

        if ($model->exists) {
            $query->in($model->getDn());
        }

        return $query->select('*')->listing()->paginate();
    }

    /**
     * Attempt to determine the objects type.
     *
     * @param Model $object
     *
     * @return string|null
     */
    protected function getObjectType(Model $object)
    {
        return (new TypeGuesser($object->getAttribute('objectclass') ?? []))->get();
    }

    /**
     * Attempt to determine the objects update timestamp.
     *
     * @param Model $object
     *
     * @return Carbon
     */
    protected function getObjectUpdatedAt(Model $object)
    {
        $attribute = 'modifytimestamp';

        if ($object instanceof ActiveDirectory) {
            $attribute = 'whenchanged';
        }

        $timestamp = $object->{$attribute};

        // We must set the timezone, as LDAP timestamps are formatted for UTC.
        return $timestamp instanceof Carbon ?
            $timestamp->setTimezone(config('app.timezone')) :
            now();
    }
}
