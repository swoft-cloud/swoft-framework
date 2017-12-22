<?php

namespace Swoft\Event;

use Swoft\Helper\PhpHelper;

/**
 * Class LazyListener - 将callable包装成对象
 * @package Swoft\Event
 * @version   2017年08月30日
 * @author    inhere <in.798@qq.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class LazyListener implements EventHandlerInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event)
    {
        return PhpHelper::call($this->callback, $event);
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
