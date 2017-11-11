<?php

namespace Swoft\Base;

use Swoft\App;

/**
 * 基类控制器
 *
 * @uses      Controller
 * @version   2017年04月30日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
abstract class Controller
{
    /**
     * @var string action方法前缀
     */
    protected $actionPrefix = 'action';

    /**
     * @var string 默认action
     */
    protected $defaultAction = 'index';

    /**
     * 执行action
     *
     * @param string $actionId action ID
     * @param array  $params   action调用参数
     * @return \Swoft\Web\Response 返回response对象
     */
    public function runAction(string $actionId, array $params = [])
    {
        return $this->runActionWithParams($actionId, $params);
    }

    /**
     * 参数运行action
     *
     * @param string $actionId action ID
     * @param array  $params   action调用参数
     * @return \Swoft\Web\Response 返回response对象
     */
    public function runActionWithParams(string $actionId, array $params = [])
    {
        $methodName = $this->getMethodName($actionId);

        // before action
        $this->beforeAction($actionId, $params);

        /* @var \Swoft\Web\Response|null $response */
        $response = $this->$methodName(...$params);

        // after action
        $this->afterAction($actionId, $params);

        return $response;
    }

    /**
     * action方法名称
     *
     * @param string $actionId action ID
     * @return string
     */
    public function getMethodName(string $actionId)
    {
        $methodName = $this->actionPrefix . ucfirst($actionId);

        if (! method_exists($this, $methodName)) {
            App::error("控制器执行action方法不存在，method=" . $methodName);
            throw new \BadMethodCallException("控制器执行action方法不存在，method=" . $methodName);
        }
        return $methodName;
    }

    /**
     * action之前
     *
     * @param string $actionId action ID
     * @param array  $params   action调用参数
     */
    protected function beforeAction(string $actionId, array $params = [])
    {
    }

    /**
     * action之后
     *
     * @param string $actionId action ID
     * @param array  $params   action调用参数
     */
    protected function afterAction(string $actionId, array $params = [])
    {
    }

    /**
     * 重定向
     *
     * @param string   $url
     * @param null|int $status
     * @return Response
     */
    protected function redirect($url, $status = null)
    {
        return App::getResponse()->redirect($url, $status);
    }

    /**
     * action前缀
     *
     * @return string
     */
    public function getActionPrefix(): string
    {
        return $this->actionPrefix;
    }

}
