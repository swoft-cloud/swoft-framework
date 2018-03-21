<?php

namespace Swoft\Rpc\Client\Service;

use Swoft\Core\AbstractDataResult;

/**
 * The data result of service
 */
class ServiceDataResult extends AbstractDataResult
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