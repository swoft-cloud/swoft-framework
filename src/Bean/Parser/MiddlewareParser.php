<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Collector;


/**
 * @uses      MiddlewareParser
 * @version   2017-11-17
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class MiddlewareParser extends AbstractParser
{

    /**
     * Parse middleware annotation
     *
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param string|null $propertyValue
     * @return mixed
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = "",
        string $methodName = "",
        $propertyValue = null
    ) {
        /** @var \Swoft\Bean\Annotation\Middleware $objectAnnotation */
        if ($methodName) {
            Collector::$requestMapping[$className]['middlewares']['actions'][$methodName][] = $objectAnnotation->getClass();
        } else {
            Collector::$requestMapping[$className]['middlewares']['group'][] = $objectAnnotation->getClass();
        }
    }
}