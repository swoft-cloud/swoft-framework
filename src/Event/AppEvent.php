<?php

namespace Swoft\Event;

/**
 * 所有事件名称
 */
class AppEvent
{
    /**
     * 应用初始化加载监听器
     */
    const APPLICATION_LOADER = "applicationLoader";

    /**
     * Pipe message event
     */
    const PIPE_MESSAGE = 'pipeMessage';

    /**
     * Resource release event behind application
     */
    const RESOURCE_RELEASE = 'resourceRelease';

    /**
     * Worker start event
     */
    const WORKER_START = 'workerStart';
}
