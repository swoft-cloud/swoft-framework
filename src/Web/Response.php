<?php

namespace Swoft\Web;

use Swoft\Core\RequestContext;
use Swoft\Bean\Collector;
use Swoft\Contract\Arrayable;
use Swoft\Helper\JsonHelper;
use Swoft\Helper\StringHelper;
use Swoft\App;
use Swoft\Web\Streams\SwooleStream;

/**
 * 响应response
 *
 * @uses      Response
 * @version   2017年05月11日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Response extends \Swoft\Core\Response
{
    use ViewRendererTrait;

    /**
     * @var \Throwable|null
     */
    protected $exception;

    /**
     * swoole响应请求
     *
     * @var \Swoole\Http\Response
     */
    protected $swooleResponse;

    /**
     * 初始化响应请求
     *
     * @param \Swoole\Http\Response $response
     */
    public function __construct(\Swoole\Http\Response $response)
    {
        $this->swooleResponse = $response;
    }

    /**
     * Redirect to a URL
     *
     * @param string   $url
     * @param null|int $status
     * @return static
     */
    public function redirect($url, $status = 302)
    {
        $response = $this;
        $response = $response->withAddedHeader('Location', (string)$url)->withStatus($status);
        return $response;
    }

    /**
     * return a View format response
     *
     * @param array|Arrayable $data
     * @param null|string $template It's a default value, use Annotation template first
     * @param null|string $layout It's a default value, use Annotation layout first
     * @return \Swoft\Web\Response
     */
    public function view($data = [], $template = null, $layout = null): Response
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }
        $controllerClass = RequestContext::getContextDataByKey('controllerClass');
        $controllerAction = RequestContext::getContextDataByKey('controllerAction');
        $template = Collector::$requestMapping[$controllerClass]['view'][$controllerAction]['template'] ?? App::getAlias($template);
        $layout = Collector::$requestMapping[$controllerClass]['view'][$controllerAction]['layout'] ?? App::getAlias($layout);
        $response = $this->render($template, $data, $layout);
        // Headers
        $response = $response->withoutHeader('Content-Type')->withAddedHeader('Content-Type', 'text/html');
        return $response;
    }

    /**
     * return a Raw format response
     *
     * @param  string $data   The data
     * @param  int    $status The HTTP status code.
     * @return \Swoft\Web\Response when $data not jsonable
     */
    public function raw(string $data = '', int $status = 200): Response
    {
        $response = $this;

        // Headers
        $response = $response->withoutHeader('Content-Type')->withAddedHeader('Content-Type', 'text/plain');
        $this->getCharset() && $response = $response->withCharset($this->getCharset());

        // Content
        $data && $response = $response->withContent($data);

        // Status code
        $status && $response = $response->withStatus($status);

        return $response;
    }

    /**
     * return a Json format response
     *
     * @param  array|Arrayable $data            The data
     * @param  int             $status          The HTTP status code.
     * @param  int             $encodingOptions Json encoding options
     * @return static when $data not jsonable
     */
    public function json($data = [], int $status = 200, int $encodingOptions = 0): Response
    {
        $response = $this;

        // Headers
        $response = $response->withoutHeader('Content-Type')->withAddedHeader('Content-Type', 'application/json');
        $this->getCharset() && $response = $response->withCharset($this->getCharset());

        // Content
        if ($data && ($this->isArrayable($data) || is_string($data))) {
            is_string($data) && $data = ['data' => $data];
            $content = JsonHelper::encode($data, $encodingOptions);
            $response = $response->withContent($content);
        } else {
            $response = $response->withContent('{}');
        }

        // Status code
        $status && $response = $response->withStatus($status);

        return $response;
    }

    /**
     * return an automatic detection format response
     *
     * @param mixed $data
     * @param int   $status
     * @return static
     */
    public function auto($data = null, int $status = 200): Response
    {
        $accepts = RequestContext::getRequest()->getHeader('accept');
        $currentAccept = current($accepts);
        $controllerClass = RequestContext::getContextDataByKey('controllerClass');
        $controllerAction = RequestContext::getContextDataByKey('controllerAction');
        $template = Collector::$requestMapping[$controllerClass]['view'][$controllerAction]['template'] ?? null;
        $matchViewModel = $this->isMatchAccept($currentAccept, 'text/html') && $controllerClass && $this->isArrayable($data) && $template && ! $this->getException();
        if ($currentAccept) {
            switch ($currentAccept) {
                // View
                case $matchViewModel === true:
                    $response = $this->view($data, $status);
                    break;
                // Json
                case $this->isMatchAccept($currentAccept, 'application/json'):
                case $this->isArrayable($data):
                    ! $this->isArrayable($data) && $data = compact('data');
                    $response = $this->json($data, $status);
                    break;
                // Raw
                default:
                    $response = $this->raw((string)$data, $status);
                    break;
            }
        } else {
            $response = $this->raw((string)$data, $status);
        }
        return $response;
    }

    /**
     * 处理 Response 并发送数据
     */
    public function send()
    {
        $response = $this;

        /**
         * Headers
         */
        // Write Headers to swoole response
        foreach ($response->getHeaders() as $key => $value) {
            $this->swooleResponse->header($key, implode(';', $value));
        }

        /**
         * Cookies
         */
        // TODO: handle cookies

        /**
         * Status code
         */
        $this->swooleResponse->status($response->getStatusCode());

        /**
         * Body
         */
        $this->swooleResponse->end($response->getBody()->getContents());
    }

    /**
     * 设置Body内容，使用默认的Stream
     *
     * @param string $content
     * @return static
     */
    public function withContent($content): Response
    {
        if ($this->stream) {
            return $this;
        }

        $new = clone $this;
        $new->stream = new SwooleStream($content);
        return $new;
    }

    /**
     * 添加cookie
     *
     * @param string  $key
     * @param  string $value
     * @param int     $expire
     * @param string  $path
     * @param string  $domain
     */
    public function addCookie($key, $value, $expire = 0, $path = '/', $domain = '')
    {
        $this->swooleResponse->cookie($key, $value, $expire, $path, $domain);
    }

    /**
     * @return null|\Throwable
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \Throwable $exception
     * @return $this
     */
    public function setException(\Throwable $exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function isArrayable($value): bool
    {
        return is_array($value) || $value instanceof Arrayable;
    }

    /**
     * @param string $accept
     * @param string $keyword
     * @return bool
     */
    private function isMatchAccept(string $accept, string $keyword): bool
    {
        return StringHelper::contains($accept, $keyword) === true;
    }
}
