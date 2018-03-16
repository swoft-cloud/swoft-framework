<?php

namespace Swoft\Pool;

/**
 * Interface PoolConfigInterface
 *
 * @package Swoft\Pool
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

    /**
     * @return int
     */
    public function getMinActive(): int;

    /**
     * @return int
     */
    public function getMaxWaitTime(): int;

    /**
     * @return int
     */
    public function getMaxIdleTime(): int;
}
