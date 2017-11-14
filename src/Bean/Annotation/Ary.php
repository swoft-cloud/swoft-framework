<?php

namespace Swoft\Bean\Annotation;

/**
 * 数组分割验证
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @uses      Ary
 * @version   2017年11月13日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Ary
{
    /**
     * 字段名称
     *
     * @var string
     */
    private $name;

    /**
     * 分隔符
     *
     * @var string
     */
    private $delimiter = ",";

    /**
     * 最小值
     *
     * @var int
     */
    private $min = 0;

    /**
     * 最小值
     *
     * @var int
     */
    private $max = PHP_INT_MAX;

    /**
     * 默认值，如果是null，强制验证参数
     *
     * @var null|integer
     */
    private $default = null;


    /**
     * Ary constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
        if (isset($values['delimiter'])) {
            $this->delimiter = $values['delimiter'];
        }
        if (isset($values['min'])) {
            $this->min = $values['min'];
        }
        if (isset($values['max'])) {
            $this->max = $values['max'];
        }
        if (isset($values['default'])) {
            $this->default = $values['default'];
        }
    }
}