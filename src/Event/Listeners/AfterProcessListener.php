<?php

namespace Swoft\Event\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Event\EventInterface;
use Swoft\Event\AppEvent;
use Swoft\Event\EventHandlerInterface;

/**
 * 进程后置事件
 *
 * @Listener(AppEvent::AFTER_PROCESS)
 * @uses      AfterProcessListener
 * @version   2017年10月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AfterProcessListener implements EventHandlerInterface
{
    /**
     * 事件回调
     *
     * @param EventInterface $event      事件对象
     */
    public function handle(EventInterface $event)
    {
        // 日志初始化
        App::getLogger()->appendNoticeLog(true);
    }
}
