<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Middlewares;
use Swoft\Bean\Collector\MiddlewareCollector;

/**
 * @uses      MiddlewaresParser
 * @version   2017-11-17
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class MiddlewaresParser extends AbstractParser
{

    /**
     * Parse middlewares annotation
     *
     * @param string      $className
     * @param Middlewares $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
     * @param string|null $propertyValue
     *
     * @return mixed
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = "",
        string $methodName = "",
        $propertyValue = null
    ) {
        MiddlewareCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
        return null;
    }
}
