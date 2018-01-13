<?php

namespace Swoft\Bootstrap;

use Swoft\App;
use Swoft\Core\InitApplicationContext;
use Swoft\Bean\BeanFactory;
use Swoft\Event\AppEvent;
use Swoft\Helper\PhpHelper;
use Swoole\Process as SwooleProcess;
use Swoft\Bootstrap\Server\AbstractServer;
use Swoft\Bootstrap\Process\AbstractProcessInterface;

/**
 *
 *
 * @uses      Process
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Process
{
    /**
     * 进程列表
     *
     * @var \Swoole\Process[]
     */
    private static $processes = [];

    /**
     * 进程ID
     *
     * @var string
     */
    private static $id;

    /**
     * 获取某个进程
     *
     * @param string $name
     * @return \Swoole\Process
     */
    public static function getProcess(string $name): SwooleProcess
    {
        if (isset(self::$processes[$name])) {
            return self::$processes[$name];
        }

        return null;
    }

    /**
     * 创建一个进程
     *
     * @param AbstractServer $server serverd对象
     * @param string $processName 进程名称
     * @param string $processClassName 进程className
     * @return null|\Swoole\Process
     */
    public static function create(
        AbstractServer $server,
        string $processName,
        string $processClassName
    ) {

        /* @var AbstractProcessInterface $processClass */
        $processClass = App::getBean($processClassName);
        $processClass->setServer($server);

        // 准备工作是否完成
        $isReady = $processClass->isReady();
        if ($isReady == false) {
            return null;
        }

        // 进程属性参数
        $pipe = $processClass->isPipe();
        $iout = $processClass->isInout();

        // 创建进程
        $process = new SwooleProcess(function (SwooleProcess $process) use ($processClass, $processName) {
            // reload
            BeanFactory::reload();
            $initApplicationContext = new InitApplicationContext();
            $initApplicationContext->init();

            App::trigger(AppEvent::BEFORE_PROCESS, null, $processName, $process, null);
            PhpHelper::call([$processClass, 'run'], [$process]);
            App::trigger(AppEvent::AFTER_PROCESS);
        }, $iout, $pipe);

        return $process;
    }

    /**
     * 获取进程ID
     *
     * @return string
     */
    public static function getId(): string
    {
        return self::$id;
    }

    /**
     * 初始化进程ID
     *
     * @param string $id
     */
    public static function setId(string $id)
    {
        self::$id = $id;
    }
}