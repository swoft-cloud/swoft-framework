<?php

namespace Swoft\Pool\Provider;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;

/**
 * Consul provider
 *
 * @Bean()
 */
class ConsulProvider implements ProviderInterface
{
    /**
     * Register path
     */
    const REGISTER_PATH = '/v1/agent/service/register';

    /**
     * Discovery path
     */
    const DISCOVERY_PATH = '/v1/health/Service/';

    /**
     * Specifies the address of the consul
     *
     * @Value(name="${config.provider.consul.address}", env="${CONSUL_ADDRESS}")
     * @var string
     */
    private $address = "http://127.0.0.1";

    /**
     * Specifies the prot of the consul
     *
     * @Value(name="${config.provider.consul.port}", env="${CONSUL_PORT}")
     * @var int
     */
    private $port = 8500;

    /**
     * Specifies a unique ID for this service. This must be unique per agent. This defaults to the Name parameter if not provided.
     *
     * @Value(name="${config.provider.consul.register.id}", env="${CONSUL_REGISTER_ID}")
     * @var string
     */
    private $registerId = '';

    /**
     * Specifies the logical name of the service. Many service instances may share the same logical service name.
     *
     * @Value(name="${config.provider.consul.register.name}", env="${CONSUL_REGISTER_NAME}")
     * @var string
     */
    private $registerName = APP_NAME;

    /**
     * Specifies a list of tags to assign to the service. These tags can be used for later filtering and are exposed via the APIs.
     *
     * @Value(name="${config.provider.consul.register.tags}", env="${CONSUL_REGISTER_TAGS}")
     * @var array
     */
    private $registerTags = [];

    /**
     * Specifies to disable the anti-entropy feature for this service's tags
     *
     * @Value(name="${config.provider.consul.register.eto}", env="${CONSUL_REGISTER_ETO}")
     * @var bool
     */
    private $registerEnableTagOverride = false;

    /**
     * Specifies the address of the service
     *
     * @Value(name="${config.provider.consul.register.service.address}", env="${CONSUL_REGISTER_SERVICE_ADDRESS}")
     * @var string
     */
    private $registerAddress = 'http://127.0.0.1';

    /**
     * Specifies the port of the service
     *
     * @Value(name="${config.provider.consul.register.service.port}", env="${CONSUL_REGISTER_SERVICE_PORT}")
     * @var int
     */
    private $registerPort = 88;

    /**
     * Specifies the checked ID
     *
     * @Value(name="${config.provider.consul.register.check.id}", env="${CONSUL_REGISTER_CHECK_ID}")
     * @var string
     */
    private $registerCheckId = '';

    /**
     * Specifies the checked name
     *
     * @Value(name="${config.provider.consul.register.check.name}", env="${CONSUL_REGISTER_CHECK_NAME}")
     * @var string
     */
    private $registerCheckName = APP_NAME;

    /**
     * Specifies the checked tcp
     *
     * @Value(name="${config.provider.consul.register.check.tcp}", env="${CONSUL_REGISTER_CHECK_TCP}")
     * @var string
     */
    private $registerCheckTcp = '127.0.0.1:8099';

    /**
     * Specifies the checked interval
     *
     * @Value(name="${config.provider.consul.register.check.interval}", env="${CONSUL_REGISTER_CHECK_INTERVAL}")
     * @var int
     */
    private $registerCheckInterval = 10;

    /**
     * Specifies the checked timeout
     *
     * @Value(name="${config.provider.consul.register.check.timeout}", env="${CONSUL_REGISTER_CHECK_TIMEOUT}")
     * @var int
     */
    private $registerCheckTimeout = 1;

    /**
     * Specifies the service to list services for
     *
     * @Value(name="${config.provider.consul.discovery.name}", env="${CONSUL_DISCOVERY_NAME}")
     * @var string
     */
    private $discoveryName = APP_NAME;

    /**
     * Specifies the datacenter to query. This will default to the datacenter of the agent being queried
     *
     * @Value(name="${config.provider.consul.discovery.dc}", env="${CONSUL_DISCOVERY_DC}")
     * @var string
     */
    private $discoveryDc = "";

    /**
     * Specifies a node name to sort the node list in ascending order based on the estimated round trip time from that node
     *
     * @Value(name="${config.provider.consul.discovery.near}", env="${CONSUL_DISCOVERY_NEAR}")
     * @var string
     */
    private $discoveryNear = "";

    /**
     * Specifies the tag to filter the list. This is specifies as part of the URL as a query parameter.
     *
     * @Value(name="${config.provider.consul.discovery.tag}", env="${CONSUL_DISCOVERY_TAG}")
     * @var string
     */
    private $discoveryTag = "";

    /**
     * Specifies that the server should return only nodes with all checks in the passing state
     *
     * @Value(name="${config.provider.consul.discovery.passing}", env="${CONSUL_DISCOVERY_PASSING}")
     * @var bool
     */
    private $discoveryPassing = true;

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
        $url      = $this->getDiscoveryUrl($serviceName);
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
     * @param array  ...$params
     *
     * @return bool
     */
    public function registerService(string $serviceName, ...$params)
    {
        $hostName = gethostname();
        if (empty($this->registerId)) {
            $this->registerId = sprintf('service-%s-%s', $this->registerName, $hostName);
        }

        if (empty($this->registerCheckId)) {
            $this->registerCheckId = sprintf('check-%s-%s', $this->registerName, $hostName);
        }

        $data = [
            'ID'                => $this->registerId,
            'Name'              => $this->registerName,
            'Tags'              => $this->registerTags,
            'Address'           => $this->registerAddress,
            'Port'              => $this->registerPort,
            'EnableTagOverride' => $this->registerEnableTagOverride,
            'Check'             => [
                'id'       => $this->registerCheckId,
                'name'     => $this->registerCheckName,
                'tcp'      => $this->registerCheckTcp,
                'interval' => sprintf('%ss', $this->registerCheckInterval),
                'timeout'  => sprintf('%ss', $this->registerCheckTimeout),
            ],
        ];

        $url = sprintf('%s:%d%s', $this->address, $this->port, self::REGISTER_PATH);
        $this->putService($data, $url);

        return true;
    }

    /**
     * @param string $serviceName
     *
     * @return string
     */
    private function getDiscoveryUrl(string $serviceName): string
    {
        $query = [
            'passing' => $this->discoveryPassing,
            'dc'      => $this->discoveryDc,
            'near'    => $this->discoveryNear,
            'tag '    => $this->discoveryName,
        ];

        $queryStr    = http_build_query($query);
        $serviceName = empty($this->discoveryName) ? $serviceName : $this->discoveryName;
        $path        = sprintf('%s%s', self::DISCOVERY_PATH, $serviceName);

        return sprintf('%s:%d%s?%s', $this->address, $this->port, $path, $queryStr);
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
