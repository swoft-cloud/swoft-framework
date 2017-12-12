<?php

namespace Swoft\Http;

use Psr\Http\Message\ResponseInterface;
use Swoft\App;
use Swoft\Base\Coroutine;
use Swoft\Http\Adapter\ResponseTrait;
use Swoft\Web\AbstractResult;
use Swoft\Web\SwooleStream;

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
     * 返回数据结果
     *
     * @return ResponseInterface
     */
    public function getResult()
    {
        if (Coroutine::isSupportCoroutine()) {
            $this->client->recv();
            // TODO: build a response
            $result = $this->client->body;
            $this->client->close();
        } else {
            $status = curl_getinfo($this->client, CURLINFO_HTTP_CODE);
            $headers = curl_getinfo($this->client);
            $response = $this->createResponse()
                ->withBody(new SwooleStream($this->sendResult))
                ->withStatus($status)
                ->withHeaders($headers);
            curl_close($this->client);
        }
        App::debug("http调用结果=" . json_encode($result));
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
}
