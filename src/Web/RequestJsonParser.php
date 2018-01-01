<?php

namespace Swoft\Web;

use Psr\Http\Message\ServerRequestInterface;
use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\JsonHelper;

/**
 * the json parser of request
 *
 * @Bean()
 * @uses      RequestJsonParser
 * @version   2017年12月02日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RequestJsonParser implements RequestParserInterface
{
    /**
     * do parser
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function parser(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($request instanceof Request && strtoupper($request->getMethod()) !== 'GET') {
            $bodyStream  = $request->getBody();
            $bodyContent = $bodyStream->getContents();
            try {
                $bodyParams = JsonHelper::decode($bodyContent, true);
            } catch (\Exception $e) {
                $bodyParams = $bodyContent;
            }
            return $request->withBodyParams($bodyParams);
        }

        return $request;
    }
}
