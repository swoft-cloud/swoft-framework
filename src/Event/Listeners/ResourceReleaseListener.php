<?php

namespace Swoft\Event\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Core\RequestContext;
use Swoft\Event\AppEvent;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Event\Events\TransactionReleaseEvent;
use Swoft\Helper\PoolHelper;
use Swoft\Log\Log;

/**
 * Resource release listener
 *
 * @Listener(AppEvent::RESOURCE_RELEASE)
 */
class ResourceReleaseListener implements EventHandlerInterface
{
    /**
     * @param \Swoft\Event\EventInterface $event
     * @throws \InvalidArgumentException
     */
    public function handle(EventInterface $event)
    {
        $contextTsKey = PoolHelper::getContextTsKey();
        $connectionKey = PoolHelper::getContextCntKey();
        $tsStacks     = RequestContext::getContextDataByKey($contextTsKey, []);
        $connections   = RequestContext::getContextDataByKey($connectionKey, []);

        if (empty($connections)) {
            return;
        }

        /* @var \Swoft\Pool\ConnectionInterface $connection */
        foreach ($connections as $connection){
            if (App::isCoContext() && !$connection->isRecv()) {
                $connection->receive();
            }
        }

        if (!empty($tsStacks)) {
            $event = new TransactionReleaseEvent(AppEvent::TRANSACTION_RELEASE, $tsStacks, $connections);
            App::trigger($event);
        }

        /* @var \Swoft\Pool\ConnectionInterface $connection */
        foreach ($connections as $connectionId => $connection) {
            Log::error(sprintf('%s connection is not released ，forget to getResult() or em->close', get_class($connection)));
            $connection->release(true);
        }
    }
}
