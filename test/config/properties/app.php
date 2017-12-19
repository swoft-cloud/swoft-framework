<?php
return [
    "version"           => '1.0',
    'autoInitBean'      => true,
    'beanScan'          => [
    ],
    'I18n'              => [
        'sourceLanguage' => '@root/resources/messages/',
    ],
    'env'               => 'Base',
    'user.stelin.steln' => 'fafafa',
    'Service'           => [
        'user' => [
            'timeout' => 3000
        ]
    ],
    'db' => require dirname(__FILE__).DS."db.php",
    'cache'    => require dirname(__FILE__).DS."cache.php",
    'service'  => require dirname(__FILE__).DS."service.php",
    'provider'  => require dirname(__FILE__).DS."provider.php",
    'test'  => require dirname(__FILE__).DS."test.php",
];