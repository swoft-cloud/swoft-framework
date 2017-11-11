<?php

namespace Swoft\Web\ExceptionHandler;

/**
 * @uses      ExceptionHandlerManager
 * @version   2017-11-11
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ExceptionHandlerManager
{

    /**
     * Default exception handlers
     *
     * @var array
     */
    protected static $defaultExceptionHandlers = [
        SystemErrorHandler::class => 1,
        RuntimeExceptionHandler::class => 2,
        HttpExceptionHandler::class => 3,
    ];

    /**
     * Use to store exception handlers
     * The user defined handler priority greater than 10 is better
     *
     * @var \SplPriorityQueue
     */
    protected static $queue;

    /**
     * Handle the exception and return a response
     *
     * @param \Throwable $throwable
     * @return null|\Swoft\Web\Response
     */
    public static function handle(\Throwable $throwable)
    {
        $response = null;
        $queue = clone self::getQueue();
        while ($queue->valid()) {
            $current = $queue->current();
            $instance = new $current();
            if ($instance instanceof AbstractHandler) {
                $instance->setException($throwable);
                if ($instance->isHandle()) {
                    $response = $instance->handle();
                    $response instanceof AbstractHandler && $response = $response->toResponse();
                    break;
                }
            }
            $queue->next();
        }
        return $response;
    }

    /**
     * Get exception handler queue
     *
     * @return \SplPriorityQueue
     */
    public static function getQueue(): \SplPriorityQueue
    {
        self::initQueue();
        return self::$queue;
    }

    /**
     * Init $queue property, and add the default handlers to queue
     */
    protected static function initQueue(): void
    {
        if (! self::$queue instanceof \SplPriorityQueue) {
            self::$queue = new \SplPriorityQueue();
            foreach (self::$defaultExceptionHandlers as $handler => $priority) {
                self::$queue->insert($handler, $priority);
            }
        }
    }

}