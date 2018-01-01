<?php
/**
 * User: 黄朝晖
 * Date: 2017-11-13
 * Time: 3:09
 */

namespace Swoft\Testing;


class Application extends \Swoft\Web\Application
{
    public function __construct()
    {
        if (!App::$isInTest) {
            throw new \RuntimeException(sprintf('Is not available to use %s in non testing enviroment', __CLASS__));
        }
    }


}