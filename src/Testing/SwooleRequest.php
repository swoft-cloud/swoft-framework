<?php
/**
 * User: 黄朝晖
 * Date: 2017-11-13
 * Time: 3:29
 */

namespace Swoft\Testing;


class SwooleRequest extends \Swoole\Http\Request
{

    public $get;
    public $post;
    public $header;
    public $server;
    public $cookie;
    public $files;

    public $fd;

    /**
     * 获取非urlencode-form表单的POST原始数据
     * @return string
     */
    function rawContent()
    {
    }

}