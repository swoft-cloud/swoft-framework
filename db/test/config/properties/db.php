<?php
return [
    'master' => [
        'name' => 'master1',
        "uri"         => [
            '127.0.0.1:3301',
            '127.0.0.1:3301',
        ],
        "maxIdel"     => 1,
        "maxActive"   => 1,
        "maxWait"     => 1,
        "timeout"     => 1,
        "balancer"    => 'random1',
        "useProvider" => true,
        'provider'    => 'consul1',
    ],

    'slave' => [
        'name' => 'slave1',
        "uri"         => [
            '127.0.0.1:3301',
            '127.0.0.1:3301',
        ],
        "maxIdel"     => 1,
        "maxActive"   => 1,
        "maxWait"     => 1,
        "timeout"     => 1,
        "balancer"    => 'random1',
        "useProvider" => true,
        'provider'    => 'consul1',
    ],
];
