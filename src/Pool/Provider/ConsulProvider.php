<?php

namespace Swoft\Pool\Provider;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Http\HttpClient;

/**
 * the provider of consul
 *
 * @Bean()
 * @uses      ConsulProvider
 * @version   2017年12月16日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ConsulProvider implements ProviderInterface
{
    /**
     * adress
     *
     * @Value(name="${config.provider.consul.address}", env="${PROVIDER_CONSUL_ADDRESS}")
     * @var string
     */
    private $address = 'http://127.0.0.1:80';

    /**
     * the tags of register service
     *
     * @Value(name="${config.provider.consul.tags}", env="${PROVIDER_CONSUL_TAGS}")
     * @var array
     */
    private $tags = [];

    /**
     * the timeout of consul
     *
     * @Value(name="${config.provider.consul.timeout}", env="${PROVIDER_CONSUL_TIMEOUT}")
     * @var int
     */
    private $timeout = 300;

    /**
     * the interval of register service
     *
     * @Value(name="${config.provider.consul.interval}", env="${PROVIDER_CONSUL_INTERVAL}")
     * @var int
     */
    private $interval = 3;

    /**
     * get service list
     *
     * @param string $serviceName
     * @param array  $params
     *
     * @return array
     */
    public function getServiceList(string $serviceName, ...$params)
    {
        // consul获取健康的节点集合
        $url      = "/v1/health/Service/{$serviceName}?passing";
        $result   = HttpClient::call($url, HttpClient::GET);
        $services = json_decode($result, true);

        // 数据格式化
        $nodes = [];
        foreach ($services as $service) {
            if (!isset($service['Service'])) {
                App::warning("consul[Service] 服务健康节点集合，数据格式不不正确，Data=" . $result);
                continue;
            }
            $serviceInfo = $service['Service'];
            if (!isset($serviceInfo['Address'], $serviceInfo['Port'])) {
                App::warning("consul[Address] Or consul[Port] 服务健康节点集合，数据格式不不正确，Data=" . $result);
                continue;
            }
            $address = $serviceInfo['Address'];
            $port    = $serviceInfo['Port'];

            $uri     = implode(":", [$address, $port]);
            $nodes[] = $uri;
        }

        return $nodes;
    }

    /**
     * register service
     *
     * @param string $serviceName
     * @param string $host
     * @param int    $port
     * @param array  ...$params
     *
     * @return bool
     */
    public function registerService(string $serviceName, string $host, int $port, ...$params)
    {
        $url      = "http://" . $this->address . "/v1/agent/Service/register";
        $hostName = gethostname();
        $service  = [
            'ID'                => $serviceName . "-" . $hostName,
            'Name'              => 'user',
            'Tags'              => $this->tags,
            'Address'           => $host,
            'Port'              => $port,
            'EnableTagOverride' => false,
            'Check'             => [
                'DeregisterCriticalServiceAfter' => '90m',
                'TCP'                            => $host . ":" . $port,
                "Interval"                       => $this->interval . "s",
            ],
        ];

        $this->putService($service, $url);

        return true;
    }

    /**
     * CURL注册服务
     *
     * @param array  $service 服务信息集合
     * @param string $url     consulURI
     */
    private function putService(array $service, string $url)
    {
        $contentJson = json_encode($service);
        $headers     = [
            'Content-Type' => 'application/json',
        ];

        $ch = curl_init(); //初始化CURL句柄
        curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); //设置请求方式

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);//设置HTTP头信息
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contentJson);//设置提交的字符串
        curl_exec($ch);//执行预定义的CURL
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
        } else {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
    }
}