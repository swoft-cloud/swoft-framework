<?php

namespace Swoft\Service;

use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\JsonHelper;

/**
 * the packer of json
 *
 * @Bean()
 * @uses      JsonPacker
 * @version   2017年07月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class JsonPacker implements PackerInterface
{
    /**
     * pack data
     *
     * @param mixed $data
     *
     * @return string
     */
    public function pack($data)
    {
        return JsonHelper::encode($data, JSON_UNESCAPED_UNICODE);
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
        return JsonHelper::decode($data, true);
    }
}
