<?php

namespace Swoft\Core;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Swoft\App;
use Swoft\Helper\ArrayHelper;

/**
 * Request context
 */
class RequestContext
{
    /**
     * 请求数据共享区
     */
    const COROUTINE_DATA = "data";

    /**
     * 当前请求request
     */
    const COROUTINE_REQUEST = "request";

    /**
     * 当前请求response
     */
    const COROUTINE_RESPONSE = "response";

    /**
     * @var array 协程数据保存
     */
    private static $coroutineLocal;

    /**
     * 请求request
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public static function getRequest()
    {
        return self::getCoroutineContext(self::COROUTINE_REQUEST);
    }

    /**
     * 请求response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function getResponse()
    {
        return self::getCoroutineContext(self::COROUTINE_RESPONSE);
    }

    /**
     * 请求共享数据
     *
     * @return array
     */
    public static function getContextData()
    {
        return self::getCoroutineContext(self::COROUTINE_DATA);
    }

    /**
     * Set the object of request
     *
     * @param RequestInterface $request
     */
    public static function setRequest(RequestInterface $request)
    {
        $coroutineId = self::getCoroutineId();
        self::$coroutineLocal[$coroutineId][self::COROUTINE_REQUEST] = $request;
    }

    /**
     * Set the object of response
     *
     * @param ResponseInterface $response
     */
    public static function setResponse(ResponseInterface $response)
    {
        $coroutineId = self::getCoroutineId();
        self::$coroutineLocal[$coroutineId][self::COROUTINE_RESPONSE] = $response;
    }

    /**
     * 初始化数据共享
     *
     * @param array $contextData
     */
    public static function setContextData(array $contextData = [])
    {
        $existContext = [];
        $coroutineId  = self::getCoroutineId();
        if (isset(self::$coroutineLocal[$coroutineId][self::COROUTINE_DATA])) {
            $existContext = self::$coroutineLocal[$coroutineId][self::COROUTINE_DATA];
        }
        self::$coroutineLocal[$coroutineId][self::COROUTINE_DATA] = ArrayHelper::merge($contextData, $existContext);
    }

    /**
     * 设置或修改，当前请求数据共享值
     *
     * @param string $key
     * @param mixed  $val
     */
    public static function setContextDataByKey(string $key, $val)
    {
        $coroutineId = self::getCoroutineId();
        self::$coroutineLocal[$coroutineId][self::COROUTINE_DATA][$key] = $val;
    }

    /**
     * 获取当前请求数据一个KEY的值
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public static function getContextDataByKey(string $key, $default = null)
    {
        $coroutineId = self::getCoroutineId();
        if (isset(self::$coroutineLocal[$coroutineId][self::COROUTINE_DATA][$key])) {
            return self::$coroutineLocal[$coroutineId][self::COROUTINE_DATA][$key];
        }

        App::warning("RequestContext data数据不存在key,key=" . $key);
        return $default;
    }

    /**
     * 请求logid
     *
     * @return string
     */
    public static function getLogid()
    {
        $contextData = self::getCoroutineContext(self::COROUTINE_DATA);
        $logid = $contextData['logid'] ?? "";
        return $logid;
    }

    /**
     * 请求跨度值
     *
     * @return int
     */
    public static function getSpanid()
    {
        $contextData = self::getCoroutineContext(self::COROUTINE_DATA);
        $spanid = $contextData['spanid'] ?? 0;
        return $spanid;
    }

    /**
     * 销毁当前协程数据
     */
    public static function destroy()
    {
        $coroutineId = self::getCoroutineId();
        if (isset(self::$coroutineLocal[$coroutineId])) {
            unset(self::$coroutineLocal[$coroutineId]);
        }
    }

    /**
     * 获取协程上下文
     *
     * @param string   $name 协程KEY
     * @return mixed|null
     */
    private static function getCoroutineContext(string $name)
    {
        $coroutineId = self::getCoroutineId();
        if (! isset(self::$coroutineLocal[$coroutineId])) {
            return null;
        }

        $coroutineContext = self::$coroutineLocal[$coroutineId];
        if (isset($coroutineContext[$name])) {
            return $coroutineContext[$name];
        }
        return null;
    }

    /**
     * 协程ID
     *
     * @return int
     */
    private static function getCoroutineId()
    {
        return Coroutine::tid();
    }
}
