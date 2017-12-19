<?php

namespace Swoft\Helper;

/**
 * 进程帮助类
 *
 * @uses      ProcessHelper
 * @version   2017年11月05日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ProcessHelper
{
    /**
     * 设置当前进程名称
     *
     * @param string $title 名称
     *
     * @return bool
     */
    public static function setProcessTitle(string $title)
    {
        if (PhpHelper::isMac()) {
            return false;
        }

        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($title);
        }
        return true;
    }
}