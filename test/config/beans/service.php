<?php
return [
    'dispatcherService' =>[
        'class' => \Swoft\Rpc\Server\DispatcherService::class
    ],
    'serviceRouter' => [
        'class' => \Swoft\Rpc\Server\Router\HandlerMapping::class,
        'suffix' => 'Service', // service文件后缀
    ],
];
