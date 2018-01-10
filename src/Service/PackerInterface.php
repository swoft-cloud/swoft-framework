<?php

namespace Swoft\Service;

/**
 * the interface of packer
 *
 * @uses      PackerInterface
 * @version   2017年12月10日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface PackerInterface
{
    /**
     * pack data
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function pack($data);

    /**
     * unpack data
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function unpack($data);
}
