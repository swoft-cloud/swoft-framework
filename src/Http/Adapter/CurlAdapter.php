<?php

namespace Swoft\Http\Adapter;

use Psr\Http\Message\RequestInterface;
use Swoft\App;
use Swoft\Http\HttpResult;


/**
 * @uses      CurlAdapter
 * @version   2017-11-22
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CurlAdapter implements AdapterInterface
{

    use ResponseTrait;

    /**
     * @var array
     */
    protected $defaultOptions = [];

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return HttpResult
     * @throws \RuntimeException
     */
    public function request(RequestInterface $request, array $options = []): HttpResult
    {
        $options = $this->handleOptions($request, array_merge($this->defaultOptions, (array)$options));

        $url = (string)$request->getUri();
        $profileKey = 'http.' . $url;

        App::profileStart($profileKey);

        $resource = curl_init();

        $this->applyOptions($resource, $request, $options);
        $this->applyMethod($resource, $request);

        curl_setopt($resource, CURLOPT_URL, (string)$request->getUri()->withFragment(''));
        // Response do not contains Headers
        curl_setopt($resource, CURLOPT_HEADER, false);
        curl_setopt($resource, CURLINFO_HEADER_OUT, true);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
        // HTTPS do not verify Certificate and HOST
        curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($resource);

        App::profileEnd($profileKey);

        $errorNo = curl_errno($resource);
        $errorString = curl_error($resource);
        if (!empty($error)) {
            App::error(sprintf('HttpClient Request ERROR #%s url=%s', $errorNo, $url));
            throw new \RuntimeException($errorNo, $errorString);
        }

        $result = new HttpResult(null, $resource, $profileKey, $result, false);
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
        if (extension_loaded('curl') && function_exists('curl_version')) {
            $userAgent .= ' curl/' . \curl_version()['version'];
        }
        return $userAgent;
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
                    $options['_options'][CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST;
                    $options['_options'][CURLOPT_USERPWD] = "$value[0]:$value[1]";
                    break;
                case 'ntlm':
                    $options['_options'][CURLOPT_HTTPAUTH] = CURLAUTH_NTLM;
                    $options['_options'][CURLOPT_USERPWD] = "$value[0]:$value[1]";
                    break;
            }
        }

        // Timeout
        $timeoutRequiresNoSignal = false;
        if (isset($options['timeout'])) {
            $timeoutRequiresNoSignal |= $options['timeout'] < 1;
            $options['_options'][CURLOPT_TIMEOUT_MS] = $options['timeout'] * 1000;
        }
        if (isset($options['connect_timeout'])) {
            $timeoutRequiresNoSignal |= $options['connect_timeout'] < 1;
            $options['_options'][CURLOPT_CONNECTTIMEOUT_MS] = $options['connect_timeout'] * 1000;
        }
        if ($timeoutRequiresNoSignal && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $options['_options'][CURLOPT_NOSIGNAL] = true;
        }

        // Ip resolve
        // CURL default value is CURL_IPRESOLVE_WHATEVER
        if (isset($options['force_ip_resolve'])) {
            if ('v4' === $options['force_ip_resolve']) {
                $options['_options'][CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
            } else {
                if ('v6' === $options['force_ip_resolve']) {
                    $options['_options'][CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V6;
                }
            }
        }

        return $options;
    }

    /**
     * @param resource $resource
     * @param RequestInterface $request
     * @param array $options
     */
    private function applyOptions($resource, RequestInterface $request, array $options)
    {
        foreach ($options['_options'] ?? [] as $key => $value) {
            curl_setopt($resource, $key, $value);
        }

        curl_setopt($resource, CURLOPT_HTTPHEADER, array_merge($request->getHeaders(), (array)$options['_headers']));
    }

    /**
     * @param resource $resource
     * @param RequestInterface $request
     */
    private function applyMethod($resource, RequestInterface $request)
    {
        switch (strtoupper($request->getMethod())) {
            case 'GET':
                curl_setopt($resource, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($resource, CURLOPT_POST, true);
                curl_setopt($resource, CURLOPT_NOBODY, true);
                curl_setopt($resource, CURLOPT_POSTFIELDS, (string)$request->getBody()->getContents());
                break;
            case 'PUT' :
            case 'DELETE':
                curl_setopt($resource, CURLOPT_CUSTOMREQUEST, strtoupper($request->getMethod()));
                curl_setopt($resource, CURLOPT_POSTFIELDS, (string)$request->getBody()->getContents());
                break;
        }
    }
}