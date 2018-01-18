<?php
/**
 * User: 黄朝晖
 * Date: 2017-11-13
 * Time: 3:09
 */

namespace Swoft\Testing;

use Swoft\App;

class Application extends \Swoft\Core\Application
{
    public function __construct()
    {
        if (!App::$isInTest) {
            throw new \RuntimeException(sprintf('Is not available to use %s in non testing enviroment', __CLASS__));
        }
    }
}
