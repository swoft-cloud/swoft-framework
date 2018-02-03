<?php

namespace Swoft\Core;

/**
 * The result of sync
 */
abstract class AbstractDataResult implements ResultInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * DataResult constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
}