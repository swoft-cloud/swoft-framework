<?php
return [
    "version"      => '1.0',
    'autoInitBean' => true,
    'beanScan'     => [
        'SwoftTest\\Redis\\Pool' => BASE_PATH . "/Cases/Pool"
    ],
    'cache'        => require __DIR__ . DS . "cache.php",
];
