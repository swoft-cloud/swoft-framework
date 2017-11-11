<?php

namespace Swoft\Web;

use Swoft\Base\RequestContext;
use Swoft\Web\ExceptionHandler\ExceptionHandlerManager;
use Swoft\Web\ResponseTransformer\AbstractTransformer;
use Swoft\Web\ResponseTransformer\ArrayableJsonTransformer;
use Swoft\Web\ResponseTransformer\RawTransformer;
use Swoft\Web\ResponseTransformer\StringJsonTransformer;
use Swoft\Web\ResponseTransformer\ViewTransformer;

/**
 * Web Controller
 *
 * @uses      Controller
 * @version   2017年11月05日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
abstract class Controller extends \Swoft\Base\Controller
{

    /**
     * @return \Swoft\Web\Request
     */
    public function request(): Request
    {
        return RequestContext::getRequest();
    }

    /**
     * @return Response
     */
    public function response(): Response
    {
        return RequestContext::getResponse();
    }

    /**
     * Run action
     *
     * @param string $actionId action ID
     * @param array  $params   action parameters
     * @return Response
     */
    public function run(string $actionId, array $params = []): Response
    {
        if (empty($actionId)) {
            $actionId = $this->defaultAction;
        }
        try {
            // Run the Action of the Controller
            $response = $this->runAction($actionId, $params);
        } catch (\Throwable $t) {
            // Handle by ExceptionHandler
            $response = ExceptionHandlerManager::handle($t);
        } finally {
            if (! $response instanceof Response) {
                /**
                 * If $response is not instance of Response,
                 * usually return by Action of Controller,
                 * then the auto() method will format the result
                 * and return a suitable response
                 */
                $response = RequestContext::getResponse()->auto($response);
            }
        }
        return $response;
    }

}
