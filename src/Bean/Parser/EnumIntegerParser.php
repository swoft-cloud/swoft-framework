<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\EnumInteger;
use Swoft\Bean\Annotation\ValidatorFrom;
use Swoft\Bean\Collector;
use Swoft\Validator\EnumIntegerValidator;

/**
 * enum integer parser
 *
 * @uses      EnumIntegerParser
 * @version   2017年12月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class EnumIntegerParser extends AbstractParser
{
    /**
     * @param string      $className
     * @param EnumInteger $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
     * @param string|null $propertyValue
     *
     * @return null
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = "",
        string $methodName = "",
        $propertyValue = null
    ) {
        $from    = $objectAnnotation->getFrom();
        $name    = $objectAnnotation->getName();
        $values  = $objectAnnotation->getValues();
        $default = $objectAnnotation->getDefault();

        $params = [$values, $default];
        $from   = isset(Collector::$serviceMapping[$className]) ? ValidatorFrom::SERVICE : $from;

        Collector::$validator[$className][$methodName]['validator'][$from][$name] = [
            'validator' => EnumIntegerValidator::class,
            'params'    => $params,
        ];

        return null;
    }
}