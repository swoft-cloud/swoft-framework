<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 16/8/27
 * Time: 下午12:37
 * Use : 监听器优先级级别 部分常量
 */

namespace Swoft\Event;

/**
 * Class ListenerPriority
 * @package Swoft\Event
 */
final class ListenerPriority
{
    const MIN          = -300;
    const LOW          = -200;
    const BELOW_NORMAL = -100;
    const NORMAL       = 0;
    const ABOVE_NORMAL = 100;
    const HIGH         = 200;
    const MAX          = 300;

}// end class ListenerLevel
