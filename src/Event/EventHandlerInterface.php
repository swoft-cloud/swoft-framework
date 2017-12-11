<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/16
 * Time: 下午10:51
 */

namespace Swoft\Event;

/**
 * Interface EventHandlerInterface - 独立的事件监听器接口
 * @package Swoft\Event
 */
interface EventHandlerInterface
{
    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event);
}
