<?php

namespace Icinga\Module\Vsphere;

use Icinga\Exception\InvalidPropertyException;
use stdClass;

class Util
{
    public static function createNestedObjects($objects)
    {
        foreach ($objects as $key => $object) {
            $objects[$key] = static::createNestedObject($object);
        }

        return $objects;
    }

    protected static function createNestedObject($object)
    {
        $res = new stdClass();
        foreach ((array) $object as $key => $value) {
            $keys = explode('.', $key);
            static::setDeepValue($res, $keys, $value);
        }

        return $res;
    }

    protected static function setDeepValue($object, $keys, $value)
    {
        $key = array_shift($keys);
        if (empty($keys)) {
            $object->$key = $value;
        } else {
            if (property_exists($object, $key)) {
                if (! is_object($object->$key)) {
                    throw new InvalidPropertyException(
                        'A key can be either object or scalar: %s'
                    );
                }
            } else {
                $object->$key = new stdClass();
            }
            static::setDeepValue($object->$key, $keys, $value);
        }
    }
}
