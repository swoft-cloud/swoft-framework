<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Value;

/**
 * value注解解析器
 *
 * @uses      ValueParser
 * @version   2017年11月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ValueParser extends AbstractParser
{
    /**
     * Inject注解解析
     *
     * @param string $className
     * @param Value  $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        $injectValue = $objectAnnotation->getName();
        if (empty($injectValue)) {
            throw new \InvalidArgumentException("@value值不能为空");
        }
        list($injectProperty, $isRef) = $this->annotationResource->getTransferProperty($injectValue);
        return [$injectProperty, $isRef];
    }
}