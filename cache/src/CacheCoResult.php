<?php

namespace Swoft\Cache;

use Swoft\Core\AbstractCoResult;

/**
 * The result of cor
 */
class CacheCoResult extends AbstractCoResult
{
    /**
     * @param array ...$params
     *
     * @return mixed
     */
    public function getResult(...$params)
    {
        return $this->recv(true);
    }
}