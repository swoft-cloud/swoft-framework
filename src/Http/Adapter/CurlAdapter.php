<?php

namespace Swoft\Http\Adapter;

use Psr\Http\Message\RequestInterface;
use Swoft\App;


/**
 * @uses      CurlAdapter
 * @version   2017-11-22
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CurlAdapter implements AdapterInterface
{

    const DEFAULT_TIMEOUT = 3;

    public function request(RequestInterface $request, array $options = [])
    {
        $url = (string)$request->getUri();
        echo '<pre>';var_dump($url);echo '</pre>';exit();
        $profileKey = 'http.' . $url;

        $timeout = $options['timeout'] ?? self::DEFAULT_TIMEOUT;

        App::profileStart($profileKey);

        $curl = curl_init();

        // 设置请求的URL
        curl_setopt($curl, CURLOPT_URL, (string)$request->getUri()->withFragment(''));

        // Response 返回 Headers
        curl_setopt($curl, CURLOPT_HEADER, true);

        // 设为TRUE把curl_exec()结果转化为字符串，而不是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // HTTPS 请求不验证证书和HOST
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        // 设置连接等待时间和header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $request->getHeaders());
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        switch (strtoupper($request->getMethod())) {
            case 'GET':
                curl_setopt($curl, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_NOBODY, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, (string)$request->getBody());
                break;
            case 'PUT' :
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, (string)$request->getBody());
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($curl, CURLOPT_POSTFIELDS, (string)$request->getBody());
                break;
        }

        $result = curl_exec($curl);
        $error = curl_errno($curl);
        if (!empty($error)) {
            App::error("httpClient curl出错 url = $url error=" . $error);
        }
//        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        App::profileEnd($profileKey);
        return $result;
    }

    public function requestDefer(RequestInterface $request, array $options = [])
    {

    }
}