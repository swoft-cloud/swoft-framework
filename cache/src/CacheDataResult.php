<?php

namespace Swoft\Cache;

use Swoft\Core\AbstractDataResult;

/**
 * The result of data
 */
class CacheDataResult extends AbstractDataResult
{
    /**
     * @param array ...$params
     *
     * @return mixed
     */
    public function getResult(...$params)
    {
        $this->release();
        return $this->data;
    }
}