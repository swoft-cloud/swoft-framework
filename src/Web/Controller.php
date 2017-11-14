<?php

namespace Swoft\Web;

use Swoft\Base\RequestContext;

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
     * @param array $params action parameters
     * @return Response|array|string|\Swoft\Contract\Arrayable
     */
    public function run(string $actionId, array $params = [])
    {
        if (empty($actionId)) {
            $actionId = $this->defaultAction;
        }
        // Run the Action of the Controller
        $response = $this->runAction($actionId, $params);
        return $response;
    }

}
