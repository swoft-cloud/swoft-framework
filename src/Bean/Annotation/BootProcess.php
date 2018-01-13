<?php

namespace Swoft\Bean\Annotation;

/**
 * the process annotation of bootstrap
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @uses      BootProcess
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BootProcess
{
    /**
     * @var string
     */
    private $name = "";

    /**
     * @var int
     */
    private $num = 1;


    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }

        if (isset($values['name'])) {
            $this->name = $values['name'];
        }

        if (isset($values['num'])) {
            $this->num = $values['num'];
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
     * @return int
     */
    public function getNum(): int
    {
        return $this->num;
    }
}