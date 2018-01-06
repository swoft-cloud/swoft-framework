<?php

namespace Swoft\Event\Listeners;

use Swoft\Core\RequestContext;
use Swoft\Bean\Annotation\Listener;
use Swoft\Event\EventInterface;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\AppEvent;

/**
 * rpc请求处理之前事件
 *
 *
 * @Listener(AppEvent::BEFORE_RECEIVE)
 * @uses      BeforeReceiveListener
 * @version   2017年08月30日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BeforeReceiveListener implements EventHandlerInterface
{

    /**
     * 事件回调
     *
     * @param EventInterface $event      事件对象
     */
    public function handle(EventInterface $event)
    {
        $params = $event->getParams();

        if (!isset($params[0])) {
            return ;
        }

        $data = $params[0];
        $logid = $data['logid'] ?? uniqid();
        $spanid = $data['spanid'] ?? 0;
        $uri = $data['func'] ?? "null";

        $contextData = [
            'logid'       => $logid,
            'spanid'      => $spanid,
            'uri'         => $uri,
            'requestTime' => microtime(true),
        ];
        RequestContext::setContextData($contextData);
    }
}
