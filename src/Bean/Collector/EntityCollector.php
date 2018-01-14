<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\Column;
use Swoft\Bean\Annotation\Entity;
use Swoft\Bean\Annotation\Id;
use Swoft\Bean\Annotation\Required;
use Swoft\Bean\Annotation\Table;
use Swoft\Bean\CollectorInterface;

/**
 * the collector of entity
 *
 * @uses      EntityCollector
 * @version   2018年01月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class EntityCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $entities = [];

    /**
     * @param string $className
     * @param null   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if ($objectAnnotation instanceof Column) {
            self::collectColumn($objectAnnotation, $className, $propertyName, $propertyValue);
        } elseif ($objectAnnotation instanceof Entity) {
            self::collectEntity($className);
        } elseif ($objectAnnotation instanceof Id) {
            self::collectId($className, $propertyName);
        } elseif ($objectAnnotation instanceof Required) {
            self::collectRequired($className, $propertyName);
        } elseif ($objectAnnotation instanceof Table) {
            self::collectTable($objectAnnotation, $className);
        }
    }

    /**
     * @param Table  $objectAnnotation
     * @param string $className
     */
    private static function collectTable(Table $objectAnnotation, string $className)
    {
        $tableName = $objectAnnotation->getName();

        self::$entities[$className]['table']['name'] = $tableName;
    }

    /**
     * @param string $className
     * @param string $propertyName
     */
    private static function collectRequired(string $className, string $propertyName)
    {
        self::$entities[$className]['field'][$propertyName]['required'] = true;
    }

    /**
     * @param string $className
     * @param string $propertyName
     */
    private static function collectId(string $className, string $propertyName)
    {
        self::$entities[$className]['table']['id'] = $propertyName;
    }

    /**
     * @param string $className
     */
    private static function collectEntity(string $className)
    {
        self::$entities[$className] = [];
    }

    /**
     * @param Column $objectAnnotation
     * @param string $className
     * @param string $propertyName
     * @param mixed  $propertyValue
     */
    private static function collectColumn(Column $objectAnnotation, string $className, string $propertyName, $propertyValue)
    {
        $columnName = $objectAnnotation->getName();

        $entity                                             = [
            'type'    => $objectAnnotation->getType(),
            'length'  => $objectAnnotation->getLength(),
            'column'  => $columnName,
            'default' => $propertyValue,
        ];
        self::$entities[$className]['field'][$propertyName] = $entity;
        self::$entities[$className]['column'][$columnName]  = $propertyName;
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$entities;
    }

}