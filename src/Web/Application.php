<?php

namespace Swoft\Web;

use Psr\Http\Message\ResponseInterface;
use Swoft\App;
use Swoft\Base\RequestContext;
use Swoft\Bean\Collector;
use Swoft\Event\Event;
use Swoft\Exception\Http\RouteNotFoundException;
use Swoft\Filter\FilterChain;
use Swoft\Helper\ResponseHelper;
use Swoft\Exception\Handler\ExceptionHandlerManager;
use Swoft\Web\Middlewares;

/**
 * 应用主体
 *
 * @uses      Application
 * @version   2017年04月25日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Application extends \Swoft\Base\Application
{

}
