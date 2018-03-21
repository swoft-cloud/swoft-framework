<?php

namespace Swoft\Db;

use Swoft\Db\Helper\DbHelper;

/**
 * Db
 */
class Db
{
    /**
     * @param string $sql
     * @param string $group
     *
     * @return \Swoft\Db\QueryBuilder
     */
    public static function query(string $sql = '', string $group = Pool::GROUP): QueryBuilder
    {
        $queryBuilderClassName = DbHelper::getQueryClassNameByGroup($group);

        return new $queryBuilderClassName($group, $sql);
    }
}