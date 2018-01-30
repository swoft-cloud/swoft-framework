<?php
return [
    "version"           => '1.0',
    'autoInitBean'      => true,
    'beanScan' => [
        'Swoft\\Test\\Testing' => BASE_PATH . "/Testing",
    ],
    'bootScan' => [
        'Swoft\\Bootstrap'     => BASE_PATH . "/../src/Bootstrap",
        'Swoft\Aop'            => BASE_PATH . "/../src/Aop",
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
    'provider'  => require dirname(__FILE__).DS."provider.php",
    'test'  => require dirname(__FILE__).DS."test.php",
];
