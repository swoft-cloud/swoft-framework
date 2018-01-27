<?php

namespace Swoft\Bean\Wrapper;

use Swoft\Bean\Annotation\BootProcess;
use Swoft\Bean\Annotation\Inject;

/**
 * the wrapper of bootstrap process
 *
 * @uses      BootProcessWrapper
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BootProcessWrapper extends AbstractWrapper
{
    /**
     * 类注解
     *
     * @var array
     */
    protected $classAnnotations
        = [
            BootProcess::class,
        ];

    /**
     * 属性注解
     *
     * @var array
     */
    protected $propertyAnnotations
        = [
            Inject::class,
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
        return isset($annotations[BootProcess::class]);
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
        return isset($annotations[Inject::class]);
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
        return false;
    }
}