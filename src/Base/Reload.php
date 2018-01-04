<?php

namespace Swoft\Base;

use Swoft\App;
use Swoft\Helper\FileHelper;
use Swoft\Server\ServerInterface;
use Swoole\Event;
use Swoft\Bean\Annotation\Bean;

/**
 * 文件更新自动监听
 *
 * @Bean()
 * @uses      Reload
 * @version   2017年08月25日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Reload
{
    /**
     * 监听文件变化的路径
     *
     * @var string
     */
    private $watchDir;

    /**
     * server服务器
     * @var ServerInterface
     */
    private $server;

    /**
     * the lasted md5 of dir
     *
     * @var string
     */
    private $md5File = '';

    /**
     * the interval of scan
     *
     * @var int
     */
    private $interval = 3;

    /**
     * 初始化方法
     */
    public function init()
    {
        $this->watchDir = App::getAlias('@app');
        $this->md5File = FileHelper::md5File($this->watchDir);
    }


    /**
     * 启动监听
     */
    public function run()
    {
        while (true){
            sleep($this->interval);
            $md5File = FileHelper::md5File($this->watchDir);
            if(strcmp($this->md5File, $md5File) !== 0){
                echo "inotify开始自动reloading...\n";
                $this->server->isRunning();
                $this->server->getServer()->reload();
                echo "inotify自动成功\n";
            }
            $this->md5File = $md5File;
        }
    }

    /**
     * @param \Swoft\Server\ServerInterface $server
     */
    public function setServer(\Swoft\Server\ServerInterface $server)
    {
        $this->server = $server;
    }
}
