<?php

namespace Swoft\Service;

use Swoft\App;
use Swoft\Base\DispatcherInterface;
use Swoft\Base\RequestHandler;
use Swoft\Bean\Annotation\Bean;
use Swoft\Event\Event;
use Swoft\Helper\ResponseHelper;
use Swoft\Middleware\Service\HandlerAdapterMiddleware;
use Swoft\Middleware\Service\PackerMiddleware;
use Swoft\Middleware\Service\RouterMiddleware;
use Swoft\Router\Service\HandlerAdapter;
use Swoft\Web\Request;
use Swoole\Server;

/**
 * service dispatcher
 *
 * @uses      DispatcherService
 * @version   2017年11月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class DispatcherService implements DispatcherInterface
{
    /**
     * service middlewares
     *
     * @var array
     */
    private $middlewares
        = [

        ];

    /**
     * the tool of service packer
     *
     * @var \Swoft\Service\IPack
     */
    private $packer;

    /**
     * do dispatcher
     *
     * @param array ...$params
     */
    public function doDispatcher(...$params)
    {
        /**
         * @var Server $server
         * @var int    $fd
         * @var int    $fromid
         * @var string $data
         */
        list($server, $fd, $fromid, $data) = $params;

        try {
            // request middlewares
            $serviceRequest = $this->getRequest($server, $fd, $fromid, $data);
            $middlewares    = $this->requestMiddlewares();
            $requestHandler = new RequestHandler($middlewares, HandlerAdapterMiddleware::class);

            /* @var \Swoft\Base\Response $response */
            $response = $requestHandler->handle($serviceRequest);
            $data     = $response->getAttribute(HandlerAdapter::ATTRIBUTE);
        } catch (\Throwable $t) {
            $message = $t->getMessage()." ".$t->getFile()." ".$t->getLine();
            $data = ResponseHelper::formatData("", $message, $t->getCode());
            $data = App::getPacker()->pack($data);
        } finally {
            App::trigger(Event::AFTER_REQUEST);
            $server->send($fd, $data);
        }
    }

    public function requestMiddlewares()
    {
        return array_merge($this->firstMiddlewares(), $this->middlewares, $this->lastMiddlewares());
    }

    public function firstMiddlewares()
    {
        return [
            PackerMiddleware::class,
            RouterMiddleware::class,
        ];
    }

    public function lastMiddlewares()
    {
        return [];
    }

    public function init()
    {
        if ($this->packer == null || !is_subclass_of($this->packer, IPack::class)) {
            $this->packer = App::getBean('jsonPacker');
        }
    }

    /**
     * @return \Swoft\Service\IPack
     */
    public function getPacker(): \Swoft\Service\IPack
    {
        return $this->packer;
    }

    private function getRequest($server, $fd, $fromid, $data)
    {
        $serviceRequest = new Request('get', '/');

        return $serviceRequest->withAttribute(PackerMiddleware::ATTRIBUTE_SERVER, $server)->withAttribute(PackerMiddleware::ATTRIBUTE_FD, $fd)
            ->withAttribute(PackerMiddleware::ATTRIBUTE_FROMID, $fromid)->withAttribute(PackerMiddleware::ATTRIBUTE_DATA, $data);
    }
}