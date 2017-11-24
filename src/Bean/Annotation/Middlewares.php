<?php

namespace Swoft\Bean\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;


/**
 * @Annotation
 * @Target({"ALL"})
 * @uses      Middlewares
 * @version   2017年11月17日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Middlewares
{

    /**
     * @var array
     */
    private $middlewares = [];

    /**
     * @var string
     */
    private $group = '';

    /**
     * Middlewares constructor.
     *
     * @param array $values
     */
    public function __construct($values)
    {
        if (isset($values['value'])) {
            $this->middlewares = $values['value'];
        }
        if (isset($values['middlewares'])) {
            $this->middlewares = $values['value'];
        }
        if (isset($values['group'])) {
            $this->group = $values['value'];
        }
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param array $middlewares
     * @return Middlewares
     */
    public function setMiddlewares($middlewares)
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

}