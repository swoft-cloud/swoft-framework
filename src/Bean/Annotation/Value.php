<?php

namespace Swoft\Bean\Annotation;

/**
 * value注解
 *
 * 1. 注入值
 * 2. 注入property配置文件值
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @uses      Value
 * @version   2017年11月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Value
{
    /**
     * 注入值
     *
     * @var string
     */
    private $name = "";

    /**
     * Value constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}