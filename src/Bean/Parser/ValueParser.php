<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Value;
use Swoft\I18n\I18n;
use Swoft\Pool\Config\RedisPoolConfig;

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
        $envValue    = $objectAnnotation->getEnv();
        if (empty($injectValue) && empty($envValue)) {
            throw new \InvalidArgumentException("the name and env of @Value can't be empty! class={$className} property={$propertyName}");
        }

        $isRef = false;
        $injectProperty = null;
        if (!empty($injectValue)) {
            list($injectProperty, $isRef) = $this->annotationResource->getTransferProperty($injectValue);
        }

        if (!empty($envValue)) {
            $value = $this->getEnvValue($envValue);
            $isArray = strpos($value, ',') !== false;
            $value = !empty($value) && $isArray? explode(",", $value): $value;
            $injectProperty = ($value !== null) ? $value : $injectProperty;
            $isRef = ($value !== null) ? false : $isRef;
        }

        if($injectProperty === null){
            throw new \InvalidArgumentException("the value of @value is null class={$className} property={$propertyName}");
        }

        if($className == RedisPoolConfig::class){
//            var_dump($injectProperty, $propertyName, "\n______\n");
        }

        return [$injectProperty, $isRef];
    }

    private function getEnvValue(string $envValue)
    {
        $value = $envValue;
        if (preg_match('/^\$\{(.*)\}$/', $envValue, $match)) {
            $value = env($match[1]);
        }

        return $value;
    }
}