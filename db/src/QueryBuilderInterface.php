<?php

namespace Swoft\Db;

/**
 * Query builder interface
 */
interface QueryBuilderInterface
{
    /**
     * @return \Swoft\Core\ResultInterface
     */
    public function execute();
}
