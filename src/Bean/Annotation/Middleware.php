<?php

namespace Swoft\Bean\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Swoft\Helper\StringHelper;


/**
 * @Annotation
 * @Target({"ALL"})
 * @uses      Middleware
 * @version   2017年11月16日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Middleware
{

    /**
     * @var string
     */
    private $class = '';

    /**
     * Middleware constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->class = $this->ltrimClass($values['value']);
        }
        if (isset($values['class'])) {
            $this->class = $this->ltrimClass($values['class']);
        }
    }

    /**
     * @param string $value
     * @return string
     */
    protected function ltrimClass(string $value)
    {
        return StringHelper::startsWith($value, '\\') ? substr($value, 1) : $value;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

}