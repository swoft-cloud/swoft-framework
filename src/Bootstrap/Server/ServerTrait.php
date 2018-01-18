<?php

namespace Swoft\Bootstrap\Server;
use Swoft\App;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Collector\ServerListenerCollector;
use Swoft\Bootstrap\SwooleEvent;
use Swoft\Core\ApplicationContext;
use Swoft\Core\InitApplicationContext;
use Swoft\Helper\ProcessHelper;
use Swoole\Server;

/**
 * the trait of Server
 *
 * @uses      ServerTrait
 * @version   2018年01月07日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
trait ServerTrait
{
    /**
     * master进程启动前初始化
     *
     * @param Server $server
     */
    public function onStart(Server $server)
    {
        file_put_contents($this->serverSetting['pfile'], $server->master_pid);
        file_put_contents($this->serverSetting['pfile'], ',' . $server->manager_pid, FILE_APPEND);
        ProcessHelper::setProcessTitle($this->serverSetting['pname'] . " master process (" . $this->scriptFile . ")");
    }

    /**
     * mananger进程启动前初始化
     *
     * @param Server $server
     */
    public function onManagerStart(Server $server)
    {
        ProcessHelper::setProcessTitle($this->serverSetting['pname'] . " manager process");
    }

    /**
     * worker进程启动前初始化
     *
     * @param Server $server   server
     * @param int    $workerId workerId
     */
    public function onWorkerStart(Server $server, int $workerId)
    {
        // worker和task进程初始化
        $setting = $server->setting;
        if ($workerId >= $setting['worker_num']) {
            ApplicationContext::setContext(ApplicationContext::TASK);
            ProcessHelper::setProcessTitle($this->serverSetting['pname'] . " task process");
        } else {
            ApplicationContext::setContext(ApplicationContext::WORKER);
            ProcessHelper::setProcessTitle($this->serverSetting['pname'] . " worker process");
        }

        // reload重新加载文件
        $this->beforeOnWorkerStart($server, $workerId);
    }

    /**
     * @param string $scriptFile
     */
    public function setScriptFile(string $scriptFile)
    {
        $this->scriptFile = $scriptFile;
    }

    /**
     * swoole server start之前运行
     */
    protected function beforeStart()
    {
        $collector = ServerListenerCollector::getCollector();
        $event = SwooleEvent::ON_BEFORE_START;
        if(!isset($collector[$event]) || empty($collector[$event])){
            return ;
        }

        $beforeStartListeners = $collector[$event];
        $this->doServerListener($beforeStartListeners, $event, [$this]);
    }

    /**
     * do listener
     *
     * @param array  $listeners
     * @param string $event
     * @param array  $params
     */
    private function doServerListener(array $listeners, string $event, array $params)
    {
        foreach ($listeners as $listenerBeanName){
            $listener = App::getBean($listenerBeanName);
            $method = SwooleEvent::getHandlerFunction($event);
            $listener->$method(...$params);
        }
    }

    /**
     * worker start之前运行
     *
     * @param Server $server   server
     * @param int    $workerId workerId
     */
    private function beforeOnWorkerStart(Server $server, int $workerId)
    {
        // 加载bean
        $this->reloadBean();
    }

    /**
     * reload bean
     */
    protected function reloadBean()
    {
        BeanFactory::reload();
        $initApplicationContext = new InitApplicationContext();
        $initApplicationContext->init();
    }
}