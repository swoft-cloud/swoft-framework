<?php

namespace Swoft\Pipeline;

use Swoole\Coroutine;

/**
 * @uses      FingersCrossedProcessor
 * @version   2017年11月15日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class FingersCrossedProcessor extends AbstractProcessor
{

    /**
     * @param mixed $payload
     * @return mixed
     */
    public function process($payload)
    {
        foreach ($this->stages as $stage) {
            if (is_string($stage) && class_exists($stage)) {
                $payload = $this->createInstanceByString($stage)->process($payload);
            } elseif ($stage instanceof \Closure) {
                $payload = $stage($payload);
            } elseif (is_array($stage) && is_callable($stage)) {
                is_string($stage[0]) && $stage[0] = $this->createInstanceByString($stage[0]);
                $payload = Coroutine::call_user_func_array($stage, [$payload]);
            } elseif (is_object($stage) && $stage instanceof ProcessorInterface) {
                $payload = $stage->process($payload);
            }
        }
        return $payload;
    }

    /**
     * @param string $class
     * @return object
     */
    private function createInstanceByString(string $class)
    {
        return new $class($this->stages);
    }
}
