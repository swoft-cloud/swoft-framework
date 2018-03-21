<?php
return [
    "version"           => '1.0',
    'autoInitBean'      => true,
    'beanScan'          => [
        'Swoft\\Db\\Test\\Testing' => BASE_PATH."/Testing",
        'Swoft\\Db' => BASE_PATH."/../src",
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
    'db' => require dirname(__FILE__) . DS . "db.php",
];
