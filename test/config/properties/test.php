<?php
return [
    'test' => [
        'name' => 'test',
        "uri"         => [
            '127.0.0.1:6379',
            '127.0.0.1:6378',
        ],
        "maxIdel"     => 1,
        "maxActive"   => 1,
        "maxWait"     => 1,
        "timeout"     => 1,
        "balancer"    => 'b',
        "useProvider" => true,
        'provider'    => 'p',
    ],
];