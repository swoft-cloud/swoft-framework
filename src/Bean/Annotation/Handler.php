<?php

namespace Swoft\Bean\Annotation;

/**
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @uses      Handler
 * @version   2018年01月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Handler
{
    /**
     * @var string
     */
    private $exception;
}