<?php

namespace Swoft\Bootstrap;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Collector\BootstrapCollector;
use Swoft\Bootstrap\Boots\Bootable;

/**
 * the bootstrap of application
 *
 * @Bean()
 * @uses      Bootstrap
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Bootstrap implements Bootable
{
    /**
     * bootstrap
     */
    public function bootstrap()
    {
        $bootstraps = BootstrapCollector::getCollector();
        array_multisort(array_column($bootstraps, 'order'), SORT_ASC, $bootstraps);
        foreach ($bootstraps as $bootstrapBeanName => $name){
            /* @var Bootable $bootstrap*/
            $bootstrap = App::getBean($bootstrapBeanName);
            $bootstrap->bootstrap();
        }
    }
}