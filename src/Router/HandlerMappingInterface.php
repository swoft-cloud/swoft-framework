<?php

namespace Swoft\Router;

/**
 * handler mapping interface
 *
 * @uses      HandlerMappingInterface
 * @version   2017年11月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface HandlerMappingInterface
{
    const ANY_METHOD = 'ANY';

    // match result status
    const STS_FOUND = 1;
    const STS_NOT_FOUND = 2;
    const STS_METHOD_NOT_ALLOWED = 3;

    const DEFAULT_REGEX = '[^/]+';
    const DEFAULT_TWO_LEVEL_KEY = '_NO_';

    /**
     * supported Methods
     * @var array
     */
    const SUPPORTED_METHODS = [
        'ANY',
        'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEAD', 'SEARCH', 'CONNECT', 'TRACE', 'UPDATE', 'PATCH'
    ];

    /**
     * the handler of controller
     *
     * @param array ...$params
     * @return \Swoft\Router\HandlerInterface;
     */
    public function getHandler(...$params);
}