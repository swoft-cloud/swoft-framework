<?php

namespace Swoft\Http;

use Psr\Http\Message\ResponseInterface;
use Swoft\App;
use Swoft\Core\AbstractResult;
use Swoft\Http\Adapter\AdapterInterface;
use Swoft\Http\Adapter\CoroutineAdapter;
use Swoft\Http\Adapter\ResponseTrait;
use Swoft\Http\Streams\SwooleStream;

/**
 * Http结果
 *
 * @uses      HttpResult
 * @version   2017年07月15日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class HttpResult extends AbstractResult
{
    use ResponseTrait;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * 返回数据结果
     *
     * @return ResponseInterface
     */
    public function getResult()
    {
        $client = $this->client;
        if ($this->getAdapter() instanceof CoroutineAdapter) {
            $this->recv();
            $this->sendResult = $client->body;
            $client->close();
            $headers = value(function () {
                $headers = [];
                foreach ($this->client->headers as $key => $value) {
                    $exploded = explode('-', $key);
                    foreach ($exploded as &$str) {
                        $str = ucfirst($str);
                    }
                    $ucKey = implode('-', $exploded);
                    $headers[$ucKey] = $value;
                }
                return $headers;
            });
            $response = $this->createResponse()
                ->withBody(new SwooleStream($this->sendResult ?? ''))
                ->withHeaders($headers ?? [])
                ->withStatus($this->deduceStatusCode($client));
        } else {
            $status = curl_getinfo($client, CURLINFO_HTTP_CODE);
            $headers = curl_getinfo($client);
            curl_close($client);
            $response = $this->createResponse()
                ->withBody(new SwooleStream($this->sendResult ?? ''))
                ->withStatus($status)
                ->withHeaders($headers);
        }
        App::debug("HTTP request result = " . json_encode($this->sendResult));
        return $response;
    }

    /**
     * @alias getResult()
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->getResult();
    }

    /**
     * Transfer sockets error code to HTTP status code.
     *
     * TODO transfer more error code
     * @param $client
     * @return int
     */
    private function deduceStatusCode($client): int
    {
        if ($client->errCode == 110) {
            $status = 404;
        } else {
            $status = $client->statusCode;
        }
        return $status > 0 ? $status : 500;
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @param AdapterInterface $adapter
     * @return HttpResult
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }
}
