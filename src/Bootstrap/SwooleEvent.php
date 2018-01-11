<?php

namespace Swoft\Bootstrap;

/**
 * the events of swoole
 *
 * @uses      SwooleEvent
 * @version   2018年01月11日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class SwooleEvent
{
    /**
     * the port of type
     */
    const TYPE_PORT = 'port';

    /**
     * the server of type
     */
    const TYPE_SERVER = 'server';

    /**
     * the event name of start
     */
    CONST ON_START = 'start';

    /**
     * the event name of workerStart
     */
    CONST ON_WORKER_START = 'workerStart';

    /**
     * the event name of managerStart
     */
    CONST ON_MANAGER_START = 'managerStart';

    /**
     * the event name of request
     */
    CONST ON_REQUEST = 'request';

    /**
     * the event name of task
     */
    CONST ON_TASK = 'task';

    /**
     * the event name of pipeMessage
     */
    CONST ON_PIPE_MESSAGE = 'pipeMessage';

    /**
     * the event name of finish
     */
    CONST ON_FINISH = 'finish';

    /**
     * the event name of connect
     */
    CONST ON_CONNECT = 'connect';

    /**
     * the event name of receive
     */
    CONST ON_RECEIVE = 'receive';

    /**
     * the event name of close
     */
    CONST ON_CLOSE = 'close';

    /**
     * @var array
     */
    private static $handlerFuntions
        = [
            self::ON_START         => 'onStart',
            self::ON_WORKER_START  => 'onWorkerStart',
            self::ON_MANAGER_START => 'onManagerStart',
            self::ON_REQUEST       => 'onRequest',
            self::ON_TASK          => 'onTask',
            self::ON_PIPE_MESSAGE  => 'onPipeMessage',
            self::ON_FINISH        => 'onFinish',
            self::ON_CONNECT       => 'onConnect',
            self::ON_RECEIVE       => 'onReceive',
            self::ON_CLOSE         => 'onClose',
        ];

    /**
     * get handler function of event
     *
     * @param string $event
     *
     * @return string
     */
    public static function getHandlerFunction(string $event)
    {
        return self::$handlerFuntions[$event];
    }

    /**
     * @param string $event
     *
     * @return bool
     */
    public static function isSwooleEvent(string $event)
    {
        return isset(self::$handlerFuntions[$event]);
    }
}