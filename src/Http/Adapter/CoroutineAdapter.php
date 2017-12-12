<?php

namespace Swoft\Http\Adapter;

use Psr\Http\Message\RequestInterface;
use Swoft\App;
use Swoft\Http\HttpResult;
use Swoole\Coroutine\Http\Client as CoHttpClient;


/**
 * @uses      CoroutineAdapter
 * @version   2017-11-22
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CoroutineAdapter implements AdapterInterface
{

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return HttpResult
     */
    public function request(RequestInterface $request, array $options = []): HttpResult
    {
        $options = $this->handleOptions($request, array_merge($this->defaultOptions, (array)$options));

        $url = (string)$request->getUri();
        $profileKey = 'http.' . $url;

        App::profileStart($profileKey);

        list($host, $port) = $this->ipResolve($request);

        $client = new CoHttpClient($host, $port);
        $this->applyOptions($client, $request, $options);
        $this->applyMethod($client, $request);
        $client->setDefer();
        $client->execute((string)$request->getUri()->withFragment(''));

        App::profileEnd($profileKey);
        
        if (isset($client->errCode)) {
            App::error(sprintf('HttpClient Request ERROR #%s url=%s', $client->errCode, $url));
            throw new \RuntimeException($client->errCode, socket_strerror($client->errCode));
        }
        
        $result = new HttpResult(null, $client, $profileKey, $client->body);
        return $result;
    }

    /**
     * Get the adapter default user agent
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        $userAgent = 'Swoft/' . App::version();
        $userAgent .= ' Swoft/' . SWOOLE_VERSION;
        $userAgent .= ' PHP/' . PHP_VERSION;
        return $userAgent;
    }

    /**
     * DNS lookup
     *
     * @param RequestInterface $request
     * @return string
     */
    protected function ipResolve(RequestInterface $request)
    {
        $host = $request->getUri()->getHost();
        $port = $request->getUri()->getPort();
        $ipLong = ip2long($host);

        if ($ipLong !== false) {
            return $host;
        }

        // DHS Lookup
        $ip = swoole_async_dns_lookup_coro($host);
        if (!$ip) {
            App::error("DNS lookup failure, domain=" . $host);
            throw new \InvalidArgumentException("DNS lookup failure, domain=" . $host);
        }
        return [$ip, $port];
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return array
     */
    private function handleOptions(RequestInterface $request, $options)
    {
        // Auth
        if (!empty($options['auth']) && is_array($options['auth'])) {
            $value = $options['auth'];
            $type = isset($value[2]) ? strtolower($value[2]) : 'basic';
            switch ($type) {
                case 'basic':
                    $options['_headers']['Authorization'] = 'Basic '
                        . base64_encode("$value[0]:$value[1]");
                    break;
                case 'digest':
                    // TODO complete digest
                    $options['_headers']['headers'] = "$value[0]:$value[1]";
                    break;
                case 'ntlm':
                    // TODO complete ntlm
                    $options['_headers'][CURLOPT_HTTPAUTH] = CURLAUTH_NTLM;
                    $options['_headers'][CURLOPT_USERPWD] = "$value[0]:$value[1]";
                    break;
            }
        }

        // Timeout
        if (isset($options['timeout']) && is_numeric($options['timeout'])) {
            $options['_options']['timeout'] = $options['timeout'] * 1000;
        }

        return $options;
    }

    /**
     * @param CoHttpClient $client
     * @param RequestInterface $request
     * @param array $options
     */
    protected function applyOptions(CoHttpClient $client, RequestInterface $request, array $options)
    {
        $client->set($options['_options']);
        $client->setHeaders(array_merge($request->getHeaders(), $options['_headers']));
    }

    /**
     * @param CoHttpClient $client
     * @param RequestInterface $request
     * @return void
     */
    protected function applyMethod(CoHttpClient $client, RequestInterface $request)
    {
        $client->setMethod($request->getMethod());
        switch (strtoupper($request->getMethod())) {
            case 'POST':
            case 'PUT' :
            case 'DELETE':
                $client->setData((string)$request->getBody()->getContents());
                break;
        }
    }

}