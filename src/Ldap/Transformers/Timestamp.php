<?php

namespace DirectoryTree\Watchdog\Ldap\Transformers;

use LdapRecord\Models\Attributes\Timestamp as LdapTimestamp;

abstract class Timestamp extends Transformer
{
    /**
     * The LDAP timestamp type.
     *
     * @var string
     */
    protected $type;

    /**
     * Transforms an LDAP timestamp.
     *
     * @throws \LdapRecord\LdapRecordException
     *
     * @return \Carbon\Carbon[]|null
     */
    public function transform()
    {
        if ($value = $this->getFirstValue()) {
            return rescue(function () use ($value) {
                $timestamp = new LdapTimestamp($this->type);

                // We will attempt to convert the attribute value to
                // a Carbon instance. If it fails we'll report the
                // error so it can be investigated by the user.
                $converted = $timestamp->toDateTime($value);

                $timezone = config('app.timezone', 'UTC');

                return $converted ? [$converted->setTimezone($timezone)] : $this->value;
            }, $this->value);
        }

        return $this->value;
    }
}
