<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\EnumNumber;
use Swoft\Bean\Annotation\ValidatorFrom;
use Swoft\Bean\Collector;
use Swoft\Validator\EnumNumberValidator;

/**
 * enum number parser
 *
 * @uses      EnumNumberParser
 * @version   2017年12月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class EnumNumberParser extends AbstractParser
{
    /**
     * @param string      $className
     * @param EnumNumber  $objectAnnotation
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
            'validator' => EnumNumberValidator::class,
            'params'    => $params,
        ];

        return null;
    }
}