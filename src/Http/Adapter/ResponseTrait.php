<?php

namespace Swoft\Http\Adapter;

use Swoft\App;
use Swoft\Base\Response as BaseResponse;
use Swoft\Testing\Base\Response as TestingBaseResponse;

/**
 * @uses      ResponseTrait
 * @version   2017-12-08
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
trait ResponseTrait
{

    /**
     * @return BaseResponse|TestingBaseResponse
     */
    protected function createResponse()
    {
        return App::$isInTest ? new TestingBaseResponse() : new BaseResponse();
    }
}
