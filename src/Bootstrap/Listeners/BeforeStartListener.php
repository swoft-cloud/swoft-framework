<?php

namespace Swoft\Bootstrap\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bean\Collector\BootProcessCollector;
use Swoft\Bean\Collector\SwooleListenerCollector;
use Swoft\Bootstrap\Listeners\Interfaces\BeforeStartInterface;
use Swoft\Bootstrap\Process;
use Swoft\Bootstrap\Server\AbstractServer;
use Swoft\Bootstrap\SwooleEvent;
use Swoft\Exception\InvalidArgumentException;

/**
 * the listener of before server start
 *
 * @BeforeStart()
 * @uses      BeforeStartListener
 * @version   2018年01月13日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BeforeStartListener implements BeforeStartInterface
{
    /**
     * @param AbstractServer $server
     */
    public function onBeforeStart(AbstractServer &$server)
    {
        // check task
        $this->checkTask();

        // add process
        $this->addProcess($server);
    }

    /**
     * @param AbstractServer $server
     */
    private function addProcess(AbstractServer &$server)
    {
        $processes = BootProcessCollector::getCollector();

        foreach ($processes as $beanName => $processInfo) {
            $num  = $processInfo['num'];
            $name = $processInfo['name'];

            while ($num > 0) {
                $num--;
                $name = sprintf('%s-%s', $name, $num);
                $userProcess = Process::create($server, $name, $beanName);
                if ($userProcess === null) {
                    continue;
                }
                $server->getServer()->addProcess($userProcess);

            }
        }
    }

    /**
     * check task
     */
    private function checkTask( )
    {
        $settings = App::getAppProperties()->get("server");
        $settings = $settings['setting'];
        $collector = SwooleListenerCollector::getCollector();

        $isConfigTask = isset($settings['task_worker_num']) && $settings['task_worker_num'] > 0;
        $isInstallTask = isset($collector[SwooleEvent::TYPE_SERVER][SwooleEvent::ON_TASK]) && isset($collector[SwooleEvent::TYPE_SERVER][SwooleEvent::ON_FINISH]);

        if($isConfigTask && !$isInstallTask){
            throw new InvalidArgumentException("Please 'composer require swoft/task' or set task_worker_num=0 !");
        }

        if(!$isConfigTask && $isInstallTask){
            throw new InvalidArgumentException("Please set task_worker_num > 0 !");
        }
    }
}