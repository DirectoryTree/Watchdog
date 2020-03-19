<?php

namespace DirectoryTree\Watchdog\Ldap\Transformers;

use Illuminate\Support\Arr;

class AttributeTransformer extends Transformer
{
    /**
     * The attribute transformer map.
     *
     * @var array
     */
    protected $map = [
        'objectsid' => ObjectSid::class,
        'accountexpires' => WindowsIntTimestamp::class,
        'pwdlastset' => WindowsIntTimestamp::class,
        'lockouttime' => WindowsIntTimestamp::class,
        'badpasswordtime' => WindowsIntTimestamp::class,
        'whenchanged' => WindowsTimestamp::class,
        'whencreated' => WindowsTimestamp::class,
        'dscorepropagationdata' => WindowsTimestamp::class,
    ];

    /**
     * Transform the value.
     *
     * @return array
     */
    public function transform()
    {
        foreach ($this->getAttributesToTransform() as $attribute) {
            if (array_key_exists($attribute, $this->map)) {
                $transformer = $this->map[$attribute];
                $value = $this->value[$attribute];

                // Transform and replace the value with the transformed value.
                $this->value[$attribute] = (new $transformer(Arr::wrap($value)))->transform();
            }
        }

        return $this->value;
    }

    /**
     * Get the attributes to transform.
     *
     * @return array
     */
    protected function getAttributesToTransform()
    {
        return array_intersect(
            array_keys($this->value),
            array_keys($this->map)
        );
    }
}
