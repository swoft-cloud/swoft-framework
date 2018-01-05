<?php

namespace Swoft\Web;

use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Helper\ArrayHelper;

/**
 * the parser of request
 *
 * @uses      RequestParser
 * @version   2017年12月02日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RequestParser implements RequestParserInterface
{
    /**
     * the parsers
     *
     * @var array
     */
    private $parsers
        = [

        ];

    /**
     * the of header
     *
     * @var string
     */
    private $headerKey = 'Content-type';

    /**
     * parse the request
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function parser(ServerRequestInterface $request): ServerRequestInterface
    {
        $contentType = $request->getHeaderLine($this->headerKey);
        $parsers     = $this->mergeParsers();

        if (!isset($parsers[$contentType])) {
            return $request;
        }

        /* @var \Swoft\Web\RequestParserInterface $parser */
        $parserBeanName = $parsers[$contentType];
        $parser         = App::getBean($parserBeanName);

        return $parser->parser($request);
    }

    /**
     * merge default and users parsers
     *
     * @return array
     */
    private function mergeParsers()
    {
        return ArrayHelper::merge($this->parsers, $this->defaultParsers());
    }

    /**
     * default parsers
     *
     * @return array
     */
    public function defaultParsers()
    {
        return [
            'application/json' => RequestJsonParser::class,
        ];
    }
}
