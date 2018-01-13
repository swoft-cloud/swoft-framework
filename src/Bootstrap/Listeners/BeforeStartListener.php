<?php

namespace Swoft\Bootstrap\Listeners;

use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bean\Collector\BootProcessCollector;
use Swoft\Bootstrap\Listeners\Interfaces\BeforeStartInterface;
use Swoole\Server;

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
     * @param Server $server
     */
    public function onBeforeStart(Server &$server)
    {
        // add process
        $this->addProcess($server);
    }

    /**
     * @param \Swoole\Server $server
     */
    private function addProcess(Server &$server)
    {
        $processes = BootProcessCollector::getCollector();

        foreach ($processes as $beanName => $processInfo) {
            $num  = $processInfo['num'];
            $name = $processInfo['name'];

            while ($num > 0) {

                $num--;
                $name = sprintf('%s-%s', $name, $num);
                $userProcess = Process::create($this, $name, $beanName);
                if ($userProcess === null) {
                    continue;
                }
                $this->server->addProcess($userProcess);

            }
        }
    }
}