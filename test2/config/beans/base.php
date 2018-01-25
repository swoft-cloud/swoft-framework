<?php

return [
    'dispatcherServer' => [
        'class' => \Swoft\Web\DispatcherServer::class
    ],
    'application' => [
        'id'          => APP_NAME,
        'name'        => APP_NAME,
        'errorAction' => '/error/index',
        'useProvider' => false,
    ],
    'httpRouter'      => [
        'class'          => \Swoft\Http\Server\Router\HandlerMapping::class,
        'ignoreLastSep'  => false,
        'tmpCacheNumber' => 1000,
        'matchAll'       => '',
    ],
    'requestParser' =>[
        'class' => \Swoft\Http\Server\Parser\RequestParser::class
    ],
    'view'    => [
        'class'     => \Swoft\View\Base\View::class,
        'viewsPath' => "@resources/views/",
    ],
    'eventManager'    => [
        'class'     => \Swoft\Event\EventManager::class,
    ],
];
