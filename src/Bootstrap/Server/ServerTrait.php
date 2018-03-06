<?php

namespace Swoft\Bootstrap\Server;

use Swoft\App;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Collector\ServerListenerCollector;
use Swoft\Bootstrap\SwooleEvent;
use Swoft\Core\ApplicationContext;
use Swoft\Core\Coroutine;
use Swoft\Core\InitApplicationContext;
use Swoft\Event\AppEvent;
use Swoft\Helper\ProcessHelper;
use Swoft\Pipe\PipeMessage;
use Swoft\Pipe\PipeMessageInterface;
use Swoole\Server;

/**
 * Server trait
 */
trait ServerTrait
{
    /**
     * onStart event callback
     *
     * @param Server $server
     */
    public function onStart(Server $server)
    {
        file_put_contents($this->serverSetting['pfile'], $server->master_pid);
        file_put_contents($this->serverSetting['pfile'], ',' . $server->manager_pid, FILE_APPEND);
        ProcessHelper::setProcessTitle($this->serverSetting['pname'] . ' master process (' . $this->scriptFile . ')');
    }

    /**
     * onManagerStart event callback
     *
     * @param Server $server
     */
    public function onManagerStart(Server $server)
    {
        ProcessHelper::setProcessTitle($this->serverSetting['pname'] . ' manager process');
    }

    /**
     * OnWorkerStart event callback
     *
     * @param Server $server   server
     * @param int    $workerId workerId
     */
    public function onWorkerStart(Server $server, int $workerId)
    {
        // Init Worker and TaskWorker
        $setting = $server->setting;
        $isWorker = false;
        if ($workerId >= $setting['worker_num']) {
            // TaskWorker
            ApplicationContext::setContext(ApplicationContext::TASK);
            ProcessHelper::setProcessTitle($this->serverSetting['pname'] . ' task process');
        } else {
            // Worker
            $isWorker = true;
            ApplicationContext::setContext(ApplicationContext::WORKER);
            ProcessHelper::setProcessTitle($this->serverSetting['pname'] . ' worker process');
        }
        $this->beforeWorkerStart($server, $workerId, $isWorker);
    }

    /**
     * onPipeMessage event callback
     *
     * @param \Swoole\Server $server
     * @param int            $srcWorkerId
     * @param string         $message
     * @return void
     * @throws \InvalidArgumentException
     */
    public function onPipeMessage(Server $server, int $srcWorkerId, string $message)
    {
        /* @var PipeMessageInterface $pipeMessage */
        $pipeMessage = App::getBean(PipeMessage::class);
        list($type, $data) = $pipeMessage->unpack($message);

        App::trigger(AppEvent::PIPE_MESSAGE, null, $type, $data, $srcWorkerId);
    }


    /**
     * @param string $scriptFile
     */
    public function setScriptFile(string $scriptFile)
    {
        $this->scriptFile = $scriptFile;
    }

    /**
     * Bind server listeners
     *
     * @param array  $listeners
     * @param string $event
     * @param array  $params
     */
    private function bindServerListener(array $listeners, string $event, array $params)
    {
        foreach ($listeners as $listenerBeanName) {
            $listener = App::getBean($listenerBeanName);
            $method = SwooleEvent::getHandlerFunction($event);
            $listener->$method(...$params);
        }
    }

    /**
     * Before swoole server start
     */
    protected function beforeServerStart()
    {
        $collector = ServerListenerCollector::getCollector();
        $event = SwooleEvent::ON_BEFORE_START;
        if (! isset($collector[$event]) || empty($collector[$event])) {
            return;
        }

        $beforeStartListeners = $collector[$event];
        $this->bindServerListener($beforeStartListeners, $event, [$this]);
    }

    /**
     * @param \Swoole\Server $server
     * @param int            $workerId
     * @param bool           $isWorker
     */
    private function beforeWorkerStart(Server $server, int $workerId, bool $isWorker)
    {
        // Load bean
        $this->reloadBean($isWorker);
    }

    /**
     * @param bool $isWorker
     */
    protected function reloadBean(bool $isWorker)
    {
        BeanFactory::reload();
        $initApplicationContext = new InitApplicationContext();
        $initApplicationContext->init();

        if($isWorker && $this->workerLock->trylock() && env('AUTO_REGISTER', false)){
            App::trigger(AppEvent::WORKER_START);
        }
    }
}