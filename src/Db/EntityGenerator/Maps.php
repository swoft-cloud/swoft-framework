<?php

namespace Swoft\Db\EntityGenerator;

/**
 * 数据库字段映射关系
 *
 * @uses      Maps
 * @version   2017年11月14日
 * @author    caiwh <471113744@qq.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */

class Maps
{
    /**
     * @const array entity映射关系
     */
    const DB_MAPPING = [
        'int'      => 'Types::INT',
        'char'     => 'Types::STRING',
        'varchar'  => 'Types::STRING',
        'text'     => 'Types::STRING',
        'datetime' => 'Types::DATETIME',
        'float'    => 'Types::FLOAT',
        'number'   => 'Types::NUMBER',
        'decimal'  => 'Types::NUMBER',
        'bool'     => 'Types::BOOLEAN',
        'tinyint'  => 'Types::BOOLEAN',
    ];

    /**
     * @const array php映射关系
     */
    const PHP_MAPPING = [
        'int'      => 'int',
        'char'     => 'string',
        'varchar'  => 'string',
        'text'     => 'string',
        'datetime' => 'string',
        'float'    => 'float',
        'number'   => 'int',
        'decimal'  => 'int',
        'bool'     => 'bool',
        'tinyint'  => 'bool',
    ];
}
