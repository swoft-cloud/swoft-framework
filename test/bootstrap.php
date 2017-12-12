<?php
require_once dirname(dirname(__FILE__)) . "/vendor/autoload.php";
require_once dirname(dirname(__FILE__)) . '/test/config/define.php';

// init
$server = new \Swoft\Server\HttpServer();
\Swoft\Bean\BeanFactory::reload();
$initApplicationContext = new \Swoft\Base\InitApplicationContext();
$initApplicationContext->routePath = dirname(dirname(__FILE__)) . '/test/config/routes.php';
$initApplicationContext->init();
\Swoft\App::$isInTest = true;