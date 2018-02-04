<?php

namespace Swoft\Core;

use Swoft\App;
use Swoft\Console\Console;
use Swoft\Helper\PhpHelper;
use Swoft\Bootstrap\Process;
use Swoft\Task\Task;
use Swoole\Coroutine as SwCoroutine;

/**
 * @uses \Swoft\Core\Coroutine
 */
class Coroutine
{
    /**
     * Coroutine id mapping
     *
     * @var array
     * [
     *  child id => top id,
     *  child id => top id,
     *  ... ...
     * ]
     */
    private static $idMap = [];

    /**
     * Get the current coroutine ID
     *
     * @return int|string
     */
    public static function id()
    {
        $cid = SwCoroutine::getuid();
        $context = ApplicationContext::getContext();

        if ($context === ApplicationContext::WORKER || $cid !== -1) {
            return $cid;
        }
        if ($context === ApplicationContext::TASK) {
            return Task::getId();
        }
        if ($context === ApplicationContext::CONSOLE) {
            return Console::id();
        }

        return Process::getId();
    }

    /**
     * Get the top coroutine ID
     *
     * @return int|string
     */
    public static function tid()
    {
        $id = self::id();
        return self::$idMap[$id] ?? $id;
    }

    /**
     * Create a coroutine
     *
     * @param callable $cb
     *
     * @return bool
     */
    public static function create(callable $cb)
    {
        $tid = self::tid();
        return SwCoroutine::create(function () use ($cb, $tid) {
            $id = SwCoroutine::getuid();
            self::$idMap[$id] = $tid;

            PhpHelper::call($cb);
        });
    }

    /**
     * Suspend a coroutine
     *
     * @param string $corouindId
     */
    public static function suspend($corouindId)
    {
        SwCoroutine::suspend($corouindId);
    }

    /**
     * Resume a coroutine
     *
     * @param string $coroutineId
     */
    public static function resume($coroutineId)
    {
        SwCoroutine::resume($coroutineId);
    }

    /**
     * Is Support Coroutine
     * Since swoole v2.0.11, use coroutine client in cli mode is available
     *
     * @return bool
     */
    public static function isSupportCoroutine(): bool
    {
        if (swoole_version() >= '2.0.11') {
            return true;
        } else {
            return App::isWorkerStatus();
        }
    }

    /**
     * Determine if should create a coroutine when you
     * want to use a Coroutine Client, and you should
     * always use self::isSupportCoroutine() before
     * call this method.
     *
     * @return bool
     */
    public static function shouldWrapCoroutine()
    {
        return App::isWorkerStatus() && swoole_version() >= '2.0.11';
    }
}
