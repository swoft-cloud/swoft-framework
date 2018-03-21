<?php

namespace Swoft\Db\Helper;

use Swoft\Db\Bean\Collector\EntityCollector;
use Swoft\Db\Types;

/**
 * The helper of entity
 */
class EntityHelper
{
    /**
     * @param array  $result
     * @param string $className
     *
     * @return mixed
     */
    public static function resultToEntity(array $result, string $className)
    {
        if (!isset($result[0])) {
            return self::arrayToEntity($result, $className);
        }
        $entities = [];
        foreach ($result as $entityData) {
            if (!\is_array($entityData)) {
                continue;
            }
            $entities[] = self::arrayToEntity($entityData, $className);
        }

        return $entities;
    }

    /**
     * @param array  $data
     * @param string $className
     *
     * @return mixed
     */
    public static function arrayToEntity(array $data, string $className)
    {

        $entities = EntityCollector::getCollector();
        if (!isset($className)) {
            return $data;
        }
        $attrs  = [];
        $object = new $className();
        foreach ($data as $col => $value) {
            if (!isset($entities[$className]['column'][$col])) {
                continue;
            }

            $field        = $entities[$className]['column'][$col];
            $setterMethod = 'set' . ucfirst($field);

            $type  = $entities[$className]['field'][$field]['type'];
            $value = self::trasferTypes($type, $value);

            if (method_exists($object, $setterMethod)) {
                $attrs[$field] = $value;
                $object->$setterMethod($value);
            }
        }
        if (method_exists($object, 'setAttrs')) {
            $object->setAttrs($attrs);
        }

        return $object;
    }

    /**
     * @param $type
     * @param $value
     *
     * @return bool|float|int|string
     */
    public static function trasferTypes($type, $value)
    {
        if ($type === Types::INT || $type === Types::NUMBER) {
            $value = (int)$value;
        } elseif ($type === Types::STRING) {
            $value = (string)$value;
        } elseif ($type === Types::BOOLEAN) {
            $value = (bool)$value;
        } elseif ($type === Types::FLOAT) {
            $value = (float)$value;
        }

        return $value;
    }
}