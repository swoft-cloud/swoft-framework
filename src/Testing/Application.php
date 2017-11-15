<?php
/**
 * User: 黄朝晖
 * Date: 2017-11-13
 * Time: 3:09
 */

namespace Swoft\Testing;


use Swoft\App;
use Swoft\Base\RequestContext;
use Swoft\Event\Event;
use Swoft\Testing\Web\Response;
use Swoft\Web\ExceptionHandler\ExceptionHandlerManager;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

class Application extends \Swoft\Web\Application
{

    /**
     * handle request
     *
     * @param \Swoole\Http\Request $request Swoole request object
     * @param \Swoole\Http\Response $response Swoole response object
     * @return bool|\Swoft\Testing\Web\Response
     */
    public function doRequest(SwooleRequest $request, SwooleResponse $response)
    {
        // Fix Chrome ico request bug
        // TODO: Add Middleware mechanisms and move "fix the Chrome ico request bug" to middleware
        if (isset($request->server['request_uri']) && $request->server['request_uri'] === '/favicon.ico') {
            $response->end('favicon.ico');
            return false;
        }

        try {
            // Initialize Request and Response and set to RequestContent
            RequestContext::setRequest($request);
            RequestContext::setResponse($response);

            // Trigger 'Before Request' event
            App::trigger(Event::BEFORE_REQUEST);

            $swfRequest = RequestContext::getRequest();
            // Get URI and Method from request
            $uri = $swfRequest->getUri()->getPath();
            $method = $swfRequest->getMethod();

            // Dispatch action of Controller by URI and Method
            // $actionResponse = $dispatcher->dispatch($uri, $method);
            $actionResponse = $this->runController($uri, $method);
        } catch (\Throwable $t) {
            // Handle by ExceptionHandler
            $actionResponse = ExceptionHandlerManager::handle($t);
        } finally {
            if (! $actionResponse instanceof Response) {
                /**
                 * If $response is not instance of Response,
                 * usually return by Action of Controller,
                 * then the auto() method will format the result
                 * and return a suitable response
                 */
                $actionResponse = RequestContext::getResponse()->auto($actionResponse);
            }
            // Handle Response
            $actionResponse->send();

            // Trigger 'After Request' event
            App::trigger(Event::AFTER_REQUEST);
        }
        return $actionResponse;
    }
}