<?php

namespace Swoft\Event\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Bean\Collector;
use Swoft\Event\ApplicationEvent;
use Swoft\Event\Event;
use Swoft\Event\IApplicationListener;

/**
 * 应用加载事件
 *
 * @Listener(Event::APPLICATION_LOADER)
 * @uses      ApplicationLoaderListener
 * @version   2017年09月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ApplicationLoaderListener implements IApplicationListener
{
    public function onApplicationEvent(ApplicationEvent $event = null, ...$params)
    {
        // 路由自动注册
        /* @var \Swoft\Router\Http\HandlerMapping $httpRouter */
        $httpRouter = App::getBean('httpRouter');
        $requestMapping = Collector::$requestMapping;
        $serviceMapping = Collector::$serviceMapping;
        $httpRouter->registerRoutes($requestMapping);
//        $httpRouter->registerServices($serviceMapping);

        App::setProperties();
    }
}