<?php

namespace Swoft\Web;

use Psr\Http\Message\ServerRequestInterface;

/**
 * interface of request parser
 *
 * @uses      RequestParserInterface
 * @version   2017年12月02日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface RequestParserInterface
{
    /**
     * do parser
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function parser(ServerRequestInterface $request):ServerRequestInterface;
}
