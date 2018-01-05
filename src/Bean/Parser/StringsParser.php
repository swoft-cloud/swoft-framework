<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Strings;
use Swoft\Bean\Annotation\ValidatorFrom;
use Swoft\Bean\Collector;
use Swoft\Validator\StringsValidator;

/**
 * the parser of strings
 *
 * @uses      StringsParser
 * @version   2017年12月02日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class StringsParser extends AbstractParser
{
    /**
     * @param string      $className
     * @param Strings     $objectAnnotation
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
        $min     = $objectAnnotation->getMin();
        $max     = $objectAnnotation->getMax();
        $default = $objectAnnotation->getDefault();

        $params = [$min, $max, $default];
        $from   = isset(Collector::$serviceMapping[$className]) ? ValidatorFrom::SERVICE : $from;
        Collector::$validator[$className][$methodName]['validator'][$from][$name] = [
            'validator' => StringsValidator::class,
            'params'    => $params,
        ];

        return null;
    }
}
