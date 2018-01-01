<?php
return [
    'dispatcherService' =>[
        'class' => \Swoft\Service\DispatcherService::class
    ],
    'serviceRouter' => [
        'class' => \Swoft\Router\Service\HandlerMapping::class,
        'suffix' => 'Service', // service文件后缀
    ],
];