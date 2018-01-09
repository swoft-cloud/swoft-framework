<?php

namespace Swoft\Bean\Wrapper;

use Swoft\Bean\Annotation\Controller;
use Swoft\Bean\Annotation\Enum;
use Swoft\Bean\Annotation\EnumFloat;
use Swoft\Bean\Annotation\EnumInteger;
use Swoft\Bean\Annotation\EnumNumber;
use Swoft\Bean\Annotation\EnumString;
use Swoft\Bean\Annotation\Floats;
use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\Integer;
use Swoft\Bean\Annotation\Middleware;
use Swoft\Bean\Annotation\Middlewares;
use Swoft\Bean\Annotation\Number;
use Swoft\Bean\Annotation\RequestMapping;
use Swoft\Bean\Annotation\Strings;
use Swoft\Bean\Annotation\Value;
use Swoft\Bean\Annotation\View;

/**
 * 路由注解封装器
 *
 * @uses      ControllerWrapper
 * @version   2017年09月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ControllerWrapper extends AbstractWrapper
{
    /**
     * 类注解
     *
     * @var array
     */
    protected $classAnnotations
        = [
            Controller::class,
            Middlewares::class,
            Middleware::class,
        ];

    /**
     * 属性注解
     *
     * @var array
     */
    protected $propertyAnnotations
        = [
            Inject::class,
            Value::class,
        ];

    /**
     * 方法注解
     *
     * @var array
     */
    protected $methodAnnotations
        = [
            RequestMapping::class,
            View::class,
            Middlewares::class,
            Middleware::class,
            Strings::class,
            Floats::class,
            Number::class,
            Integer::class,
            Enum::class
        ];

    /**
     * 是否解析类注解
     *
     * @param array $annotations
     *
     * @return bool
     */
    public function isParseClassAnnotations(array $annotations)
    {
        return isset($annotations[Controller::class]);
    }

    /**
     * 是否解析属性注解
     *
     * @param array $annotations
     *
     * @return bool
     */
    public function isParsePropertyAnnotations(array $annotations)
    {
        return isset($annotations[Inject::class]) || isset($annotations[Value::class]);
    }

    /**
     * 是否解析方法注解
     *
     * @param array $annotations
     *
     * @return bool
     */
    public function isParseMethodAnnotations(array $annotations)
    {
        return true;
    }
}
