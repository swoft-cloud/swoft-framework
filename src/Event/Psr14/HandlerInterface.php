<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/16
 * Time: 下午10:51
 */

namespace Swoft\Event;

/**
 * Interface ListenerInterface
 * @package Swoft\Event
 */
interface HandlerInterface
{
    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event);
}
