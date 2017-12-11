<?php

namespace Swoft\Base;

use Swoft\App;

/**
 * 应用基类
 *
 * @uses      Application
 * @version   2017年04月25日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
abstract class Application
{
    /**
     * @var string 应用ID
     */
    protected $id;

    /**
     * @var string 应用名称
     */
    protected $name;

    /**
     * 错误action，统一错误处理
     *
     * @var string
     */
    protected $errorAction;

    /**
     * @var bool 是否使用第三方(consul/etcd/zk)注册服务
     */
    protected $useProvider = false;

    /**
     * 初始化
     */
    public function init()
    {
        App::$app = $this;
    }

    /**
     * 获取errorAction
     *
     * @return string
     */
    public function getErrorAction(): string
    {
        return $this->errorAction;
    }
}
