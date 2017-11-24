<?php

namespace Swoft\Bean\Annotation;

/**
 * 整数枚举类型验证
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @uses      EnumInteger
 * @version   2017年11月13日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class EnumInteger
{
    /**
     * 字段名称
     *
     * @var string
     */
    private $name;

    /**
     * 枚举值集合
     *
     * @var array
     */
    private $values;

    /**
     * 默认值，如果是null，强制验证参数
     *
     * @var null|integer
     */
    private $default = null;

    /**
     * EnumStr constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
        if (isset($values['values'])) {
            $this->values = $values['values'];
        }
        if (isset($values['default'])) {
            $this->default = $values['default'];
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return int|null
     */
    public function getDefault()
    {
        return $this->default;
    }
}