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
     * @param string $group
     *
     * @return string
     */
    public static function getGroupKey(string $group):string
    {
        $cid = Coroutine::id();

        return sprintf('%d-%s', $cid, $group);
    }
}