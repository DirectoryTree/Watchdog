<?php

namespace DirectoryTree\Watchdog\Ldap\Transformers;

use Illuminate\Support\Arr;
use UnexpectedValueException;

class AttributeTransformer extends Transformer
{
    /**
     * The default attribute transformers.
     *
     * @var array
     */
    protected $default = [
        'objectsid'     => ObjectSid::class,
        'windows'       => WindowsTimestamp::class,
        'windows-int'   => WindowsIntTimestamp::class,
    ];

    /**
     * Transform the value.
     *
     * @return array
     */
    public function transform()
    {
        $transform = config('watchdog.attributes.transform', [
            'pwdlastset' => 'windows-int',
        ]);

        $attributesToTransform = array_intersect(
            array_keys($this->value),
            array_keys($transform)
        );

        foreach ($attributesToTransform as $attribute) {
            if (array_key_exists($attribute, $this->value)) {
                $transformer = $this->transformer($transform[$attribute], Arr::wrap($this->value[$attribute]));

                $this->value[$attribute] = $transformer->transform();
            }
        }

        return $this->value;
    }

    /**
     * Get the class name of the transformer.
     *
     * @param string $name
     * @param array  $value
     *
     * @return Transformer
     *
     * @throws UnexpectedValueException
     */
    protected function transformer($name, $value)
    {
        $default = $this->default[$name] ?? null;

        $transformer = config("watchdog.attributes.transformers.$name", $default);

        if (!class_exists($transformer)) {
            throw new \UnexpectedValueException("Transformer [$name] does not exist.");
        }

        return (new $transformer($value));
    }
}
