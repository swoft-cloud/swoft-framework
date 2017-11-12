<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\View;
use Swoft\Bean\Collector;

/**
 * @uses      ViewParser
 * @version   2017-11-08
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ViewParser extends AbstractParser
{

    /**
     * 解析注解
     *
     * @param string      $className
     * @param View        $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
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
        if (isset(Collector::$requestMapping[$className])) {
            Collector::$requestMapping[$className]['view'] = [
                'template' => $objectAnnotation->getTemplate(),
                'layout' => $objectAnnotation->getLayout(),
            ];
        }
    }
}