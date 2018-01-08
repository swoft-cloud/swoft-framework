<?php

namespace Swoft\Event\Listeners;

use Swoft\Aop\Aop;
use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Bean\Collector;
use Swoft\Event\EventInterface;
use Swoft\Event\AppEvent;
use Swoft\Event\EventHandlerInterface;

/**
 * 应用加载事件
 *
 * @Listener(AppEvent::APPLICATION_LOADER)
 * @uses      ApplicationLoaderListener
 * @version   2017年09月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ApplicationLoaderListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event      事件对象
     */
    public function handle(EventInterface $event)
    {
//        /* @var \Swoft\Router\Http\HandlerMapping $httpRouter */
//        $httpRouter = App::getBean('httpRouter');
//        /* @var \Swoft\Router\Service\HandlerMapping $serviceRouter */
//        $serviceRouter = App::getBean('serviceRouter');
//
//        $requestMapping = Collector::$requestMapping;
//        $serviceMapping = Collector::$serviceMapping;
//
//        $httpRouter->registerRoutes($requestMapping);
//        $serviceRouter->register($serviceMapping);

        App::setProperties();
    }
}
