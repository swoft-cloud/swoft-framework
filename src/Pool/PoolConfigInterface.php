<?php

namespace Swoft\Pool;

/**
 * the interface of pool config
 *
 * @uses      PoolConfigInterface
 * @version   2017年12月16日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface PoolConfigInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return int
     */
    public function getMaxIdel(): int;

    /**
     * @return int
     */
    public function getMaxActive(): int;

    /**
     * @return int
     */
    public function getMaxWait(): int;

    /**
     * @return int
     */
    public function getTimeout(): int;

    /**
     * @return array
     */
    public function getUri(): array;

    /**
     * @return bool
     */
    public function isUseProvider(): bool;

    /**
     * @return string
     */
    public function getBalancer(): string;

    /**
     * @return string
     */
    public function getProvider(): string;
}