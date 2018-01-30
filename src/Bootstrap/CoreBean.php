<?php

namespace Swoft\Bootstrap;

use Monolog\Formatter\LineFormatter;
use Swoft\App;
use Swoft\Bean\Annotation\BootBean;
use Swoft\Core\Application;
use Swoft\Core\Config;
use Swoft\Core\BootBeanIntereface;
use Swoft\Event\EventManager;
use Swoft\Log\Logger;
use Swoft\Pool\BalancerSelector;
use Swoft\Pool\ProviderSelector;

/**
 * The corebean of swoft
 *
 * @BootBean()
 */
class CoreBean implements BootBeanIntereface
{
    public function beans()
    {
        return [
            'config'           => [
                'class'      => Config::class,
                'properties' => value(function () {
                    $config     = new Config();
                    $properties = [];
                    $dir        = App::getAlias('@properties');
                    if (is_readable($dir)) {
                        $config->load($dir);
                        $properties = $config->toArray();
                    }

                    return $properties;
                }),
            ],
            'application'      => [
                'class' => Application::class,
            ],
            'eventManager'     => [
                'class' => EventManager::class,
            ],
            'balancerSelector' => [
                'class' => BalancerSelector::class,
            ],
            'providerSelector' => [
                'class' => ProviderSelector::class,
            ],
            'logger'           => [
                'class'         => Logger::class,
                'name'          => APP_NAME,
                'flushInterval' => 100000,
                'flushRequest'  => false,
                'handlers'      => [],
            ],
            'lineFormatter'    => [
                'class'      => LineFormatter::class,
                'format'     => '%datetime% [%level_name%] [%channel%] [logid:%logid%] [spanid:%spanid%] %messages%',
                'dateFormat' => 'Y/m/d H:i:s',
            ],
        ];
    }
}