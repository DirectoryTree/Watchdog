<?php

namespace DirectoryTree\Watchdog\Jobs;

use Exception;
use Carbon\Carbon;
use LdapRecord\Models\Model;
use Illuminate\Support\Facades\DB;
use DirectoryTree\Watchdog\LdapScan;
use DirectoryTree\Watchdog\LdapScanEntry;
use LdapRecord\Models\Types\ActiveDirectory;
use DirectoryTree\Watchdog\Ldap\TypeResolver;

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
        $this->scan->update(['started_at' => now()]);

        $this->scan->progress()->create(['state' => LdapScan::STATE_IMPORTING]);

        info("Starting to scan domain [{$this->scan->watcher->name}]");

        // We'll initialize a database transaction so all of our
        // inserts and updates are pushed at once. Otherwise
        // each update or insert would be done separately,
        // becoming very resource intensive.
        DB::transaction(function () {
            // Here we will attempt to retrieve the Root DSE record of the
            // domain to be able to properly assign the parent for each
            // child object that is imported, as well as retrieving
            // domain information such as password expiry time.
            if ($rootDse = $this->createModel()->read()->first()) {
                $parent = $this->import($rootDse);
            }

            $this->run($this->createModel(), $parent ?? null);
        });

        $imported = count($this->guids);

        // Upon successful completion, we'll update our scan
        // stats to ensure it is not processed again.
        $this->scan->update(['imported'  => $imported]);

        $this->scan->progress()->create(['state' => LdapScan::STATE_IMPORTED]);

        info("Successfully completed scan. Imported [$imported] record(s).");
    }

    /**
     * Run the import.
     *
     * @param Model              $model
     * @param LdapScanEntry|null $parent
     *
     * @return void
     */
    protected function run(Model $model, LdapScanEntry $parent = null)
    {
        $this->query($model)->reject(function (Model $object) {
            return empty($object->getConvertedGuid());
        })->each(function (Model $object) use ($parent) {
            $this->import($object, $parent);
        });
    }

    /**
     * Import the LDAP objects on the given connection.
     *
     * @param Model              $object
     * @param LdapScanEntry|null $parent
     *
     * @return LdapScanEntry
     */
    protected function import(Model $object, LdapScanEntry $parent = null)
    {
        /** @var LdapScanEntry $entry */
        $entry = $this->scan->entries()->make();

        $type = $this->getObjectType($object);

        $entry->parent()->associate(optional($parent)->id);

        $entry->type = $type;
        $entry->dn = $object->getDn();
        $entry->name = $object->getName();
        $entry->guid = $object->getConvertedGuid();
        $entry->values = $this->getObjectValues($object);
        $entry->ldap_updated_at = $this->getObjectUpdatedAt($object);

        $entry->save();

        $this->guids[] = $object->getConvertedGuid();

        // If the object is a container, we will
        // recursively import its descendants.
        if ($type == TypeResolver::TYPE_CONTAINER) {
            $this->run($object, $entry);
        }

        return $entry;
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
        $query = $model->exists ? $model->descendants() : $model->newQuery()->listing();

        return $query->select('*')->paginate();
    }

    /**
     * Get all of the objects values
     *
     * @param Model $object
     *
     * @return array
     */
    protected function getObjectValues(Model $object)
    {
        $values = $object->jsonSerialize();

        ksort($values);

        return $values;
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
        return (new TypeResolver($object->getAttribute('objectclass') ?? []))->get();
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
}
