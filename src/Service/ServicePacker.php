<?php

namespace Swoft\Service;

use Swoft\App;
use Swoft\Core\RequestContext;
use Swoft\Exception\ServiceException;
use Swoft\Helper\JsonHelper;

/**
 * the packer of rpc
 *
 * @uses      ServicePacker
 * @version   2017年12月10日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ServicePacker implements PackerInterface
{
    /**
     * the type of packer
     *
     * @var string
     */
    private $type = 'json';

    /**
     * the packers
     *
     * @var array
     */
    private $packers
        = [

        ];

    /**
     * pack data
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function pack($data)
    {
        $packer = $this->getPacker();

        return $packer->pack($data);
    }

    /**
     * unpack data
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function unpack($data)
    {
        $packer = $this->getPacker();

        return $packer->unpack($data);
    }

    /**
     * get packer from config
     *
     * @return PackerInterface
     * @throws \Swoft\Exception\ServiceException
     */
    public function getPacker()
    {
        $packers = $this->mergePackers();
        if (!isset($packers[$this->type])) {
            throw new ServiceException("the $this->type of packer in not exist!");
        }
        $packerName = $packers[$this->type];
        $packer     = App::getBean($packerName);
        if (!($packer instanceof PackerInterface)) {
            throw new ServiceException("the $this->type of packer in not instance of PackerInterface!");
        }

        return $packer;
    }

    /**
     * format the data of packer
     *
     * @param string $func   函数
     * @param array  $params 参数
     *
     * @return array
     */
    public function formatData(string $func, array $params)
    {
        $logid  = RequestContext::getLogid();
        $spanid = RequestContext::getSpanid() + 1;

        // 传递数据信息
        $data = [
            'func'   => $func,
            'params' => $params,
            'logid'  => $logid,
            'spanid' => $spanid,
        ];

        return $data;
    }

    /**
     * validate the data of packer
     *
     * @param array $data 参数
     *
     * @return mixed
     * @throws ServiceException
     */
    public function checkData(array $data)
    {
        // check formatter
        if (!isset($data['status']) || !isset($data['data']) || !isset($data['msg'])) {
            throw new ServiceException("the return of rpc is incorrected，data=" . JsonHelper::encode($data, JSON_UNESCAPED_UNICODE));
        }

        // check status
        $status = $data['status'];
        if ($status != 200) {
            throw new ServiceException("the return status of rpc is incorrected，data=" . JsonHelper::encode($data, JSON_UNESCAPED_UNICODE));
        }

        return $data['data'];
    }

    /**
     * merge default and config packers
     *
     * @return array
     */
    public function mergePackers()
    {
        return array_merge($this->packers, $this->defaultPackers());
    }

    /**
     * the packers of deafault
     *
     * @return array
     */
    public function defaultPackers()
    {
        return [
            'json' => JsonPacker::class,
        ];
    }
}
