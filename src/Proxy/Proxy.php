<?php

namespace Swoft\Proxy;

use Swoft\Helper\StringHelper;
use Swoft\Proxy\Handler\HandlerInterface;

/**
 * the class of proxy
 *
 * @uses      Proxy
 * @version   2017年12月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Proxy
{
    /**
     * return a proxy instance
     *
     * @param string           $className
     * @param HandlerInterface $handler
     *
     * @return object
     */
    public static function newProxyInstance(string $className, HandlerInterface $handler)
    {
        $reflectionClass   = new \ReflectionClass($className);
        $reflectionMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED);

        // the template of methods
        $id             = uniqid();
        $proxyClassName = basename(str_replace("\\", '/', $className));
        $proxyClassName = $proxyClassName . "_" . $id;

        $template
            = "class $proxyClassName extends $className {
            private \$hanadler;
            public function __construct(\$handler)
            {
                \$this->hanadler = \$handler;
            }
        ";

        $template .= self::getMethodsTemplate($reflectionMethods);
        $template .= "}";

        eval($template);

        $newRc = new \ReflectionClass($proxyClassName);

        return $newRc->newInstance($handler);
    }

    /**
     * @param \ReflectionMethod[] $reflectionMethods
     *
     * @return string
     */
    private static function getMethodsTemplate(array $reflectionMethods): string
    {
        $template = "";
        foreach ($reflectionMethods as $reflectionMethod) {
            $methodName = $reflectionMethod->getName();

            if (!$reflectionMethod->isPublic() || StringHelper::startsWith($methodName, '__') || $reflectionMethod->isStatic()) {
                continue;
            }
            if ($reflectionMethod->isProtected()) {
                $template .= " protected function $methodName (";
            } else {
                $template .= " public function $methodName (";
            }

            list($paramTemplate, $params) = self::getParameterTemplate($reflectionMethod);
            $template               .= $paramTemplate;
            $template               .= ' ) ';
            $reflectionMethodReturn = $reflectionMethod->getReturnType();
            if ($reflectionMethodReturn !== null) {
                $returnType = $reflectionMethodReturn->__toString();
                $returnType = ($returnType == 'self')?$reflectionMethod->getDeclaringClass()->getName():$returnType;
                $template .= " : $returnType";
            }

            $paramsStr = implode(',', $params);
            $template.= "{
                \$params = func_get_args();
                return \$this->hanadler->invoke('{$methodName}', \$params);
            }
            ";
        }

        return $template;
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return array
     */
    private static function getParameterTemplate(\ReflectionMethod $reflectionMethod): array
    {
        $template             = "";
        $params               = [];
        $reflectionParameters = $reflectionMethod->getParameters();
        $paramCount           = count($reflectionParameters);
        foreach ($reflectionParameters as $reflectionParameter) {
            $paramCount--;
            $type = $reflectionParameter->getType();
            if ($type !== null) {
                $type     = $type->__toString();
                $template .= " $type ";
            }
            $paramName = $reflectionParameter->getName();
            if($reflectionParameter->isPassedByReference()){
                $template  .= " &\${$paramName} ";
            }elseif($reflectionParameter->isVariadic()){
                $template  .= " ...\${$paramName} ";
            }else{
                $template  .= " \${$paramName} ";
            }

            if($reflectionParameter->isOptional() && $reflectionParameter->isVariadic() == false){
                $template .= self::getMethodDefault($reflectionParameter);
            }

            if ($paramCount !== 0) {
                $template .= ',';
            }

            $params[] = "\${$paramName}";
        }

        return [$template, $params];
    }

    private static function getMethodDefault(\ReflectionParameter $reflectionParameter)
    {
        $template = "";
        $defaultValue = $reflectionParameter->getDefaultValue();
        if($reflectionParameter->isDefaultValueConstant()){
            $defaultConst = $reflectionParameter->getDefaultValueConstantName();
            $template = " = {$defaultConst}";
        }elseif(is_bool($defaultValue)){
            $value = ($defaultValue)?"true":"false";
            $template = " = {$value}";
        }elseif(is_string($defaultValue)){
            $template = " = ''";
        }elseif(is_int($defaultValue)){
            $template = " = 0";
        }elseif(is_array($defaultValue)){
            $template = " = []";
        }elseif(is_float($defaultValue)){
            $template = " = []";
        }elseif(is_object($defaultValue) || is_null($defaultValue)){
            $template = " = null";
        }
        return $template;
    }
}


