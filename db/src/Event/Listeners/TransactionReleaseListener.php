<?php

namespace Swoft\Db\Event\Listeners;

use Swoft\Bean\Annotation\Listener;
use Swoft\Db\AbstractDbConnection;
use Swoft\Event\AppEvent;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Event\Events\TransactionReleaseEvent;
use Swoft\Log\Log;

/**
 * TransactionRelease
 *
 * @Listener(AppEvent::TRANSACTION_RELEASE)
 */
class TransactionReleaseListener implements EventHandlerInterface
{
    public function handle(EventInterface $event)
    {
        if (!($event instanceof TransactionReleaseEvent)) {
            return;
        }

        $tsStacks = $event->getTsStacks();
        $connections = $event->getConnections();
        foreach ($tsStacks as $tsStack) {
            if (!($tsStack instanceof \SplStack)) {
                continue;
            }
            while (!$tsStack->isEmpty()) {
                $connectId = $tsStack->pop();
                if (!isset($connections[$connectId])) {
                    continue;
                }
                $connection = $connections[$connectId];
                if ($connection instanceof AbstractDbConnection) {
                    $connection->rollback();
                    $connections[$connectId] = $connection;

                    Log::error(sprintf('%s transaction is not committed or rollbacked', get_class($connection)));
                }
            }
        }
    }
}