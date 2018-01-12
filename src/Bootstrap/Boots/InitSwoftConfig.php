<?php

namespace Swoft\Bootstrap\Boots;

use Swoft\App;
use Swoft\Bean\Annotation\Bootstrap;
use Swoft\Helper\StringHelper;
use Swoft\Bootstrap\Server\AbstractServer;

/**
 * @Bootstrap(order=5)
 * @uses      InitSwoftConfig
 * @version   2017-11-02
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class InitSwoftConfig implements Bootable
{
    public function bootstrap()
    {
        $server = App::$server;
        if ($server instanceof AbstractServer) {
            /** @var array[] $settings */
            $settings = App::getAppProperties()->get('server');
            if (! isset($settings['tcp'])) {
                throw new \InvalidArgumentException('未配置tcp启动参数，settings=' . json_encode($settings));
            }

            if (! isset($settings['http'])) {
                throw new \InvalidArgumentException('未配置http启动参数，settings=' . json_encode($settings));
            }

            if (! isset($settings['server'])) {
                throw new \InvalidArgumentException('未配置server启动参数，settings=' . json_encode($settings));
            }

            if (! isset($settings['setting'])) {
                throw new \InvalidArgumentException('未配置setting启动参数，settings=' . json_encode($settings));
            }

            foreach ($settings['setting'] as $key => &$value) {
                if (\is_string($value) && StringHelper::contains($value, ['@'])) {
                    $value = App::getAlias($value);
                }
            }

            $server->tcpSetting = $settings['tcp'];
            $server->httpSetting = $settings['http'];
            $server->serverSetting = $settings['server'];
            $server->processSetting = $settings['process'];
            $server->crontabSetting = $settings['crontab'];
            $server->setting = $settings['setting'];
        }
    }
}
