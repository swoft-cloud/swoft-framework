<?php

namespace Swoft\Process;

use Swoft\App;
use Swoft\Core\Reload;
use Swoole\Process;

/**
 * reload进程
 *
 * @uses      ReloadProcess
 * @version   2017年10月21日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ReloadProcess extends AbstractProcessInterface
{

    /**
     * inout
     *
     * @var bool
     */
    protected $inout = false;

    /**
     * 运行进程逻辑
     *
     * @param Process $process
     */
    public function run(Process $process)
    {
        $pname = $this->server->getPname();
        $processName = "$pname reload process";
        $process->name($processName);

        /* @var Reload $relaod */
        $relaod = App::getBean(Reload::class);
        $relaod->setServer($this->server);
        $relaod->run();
    }

    /**
     * 进程启动准备工作
     *
     * @return bool
     */
    public function isReady(): bool
    {
        if (! App::getAppProperties()->get('server.server.autoReload', false)) {
            echo '自动reload未开启，请检查配置(AUTO_RELOAD)' . PHP_EOL;
            return false;
        }

        return true;
    }
}
