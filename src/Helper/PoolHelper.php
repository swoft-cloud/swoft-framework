<?php

namespace Swoft\Helper;

use Swoft\Core\Coroutine;

/**
 * PoolHelper
 */
class PoolHelper
{
    /**
     * @return string
     */
    public static function getContextCntKey(): string
    {
        return sprintf('connectioins');
    }

    /**
     * @return string
     */
    public static function getContextTsKey(): string
    {
        return sprintf('transactions');
    }

    /**
     * @param string $poolId
     *
     * @return string
     */
    public static function getCidPoolId(string $poolId)
    {
        $cid = Coroutine::id();

        return sprintf('%d-%s', $cid, $poolId);
    }
}