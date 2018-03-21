<?php

namespace Swoft\Db;

use Swoft\App;
use Swoft\Core\AbstractCoResult;
use Swoft\Db\Helper\EntityHelper;

/**
 * Class DbCoResult
 *
 * @package Swoft\Db
 */
class DbCoResult extends AbstractCoResult
{
    /**
     * Is insert operation
     *
     * @var bool
     */
    private $insert = false;

    /**
     * Is update or delete operation
     *
     * @var bool
     */
    private $updateOrDelete = false;

    /**
     * Is find one entity operation
     *
     * @var bool
     */
    private $findOne = false;

    /**
     * @param array ...$params
     *
     * @return mixed
     */
    public function getResult(...$params)
    {
        $className = '';
        if (!empty($params)) {
            list($className) = $params;
        }

        $result = $this->recv(true);
        $result = $this->transferResult($result);

        // Logger
        list(, $sqlId) = explode('.', $this->profileKey);
        App::debug("SQL语句执行结果(defer) sqlId=$sqlId result=" . json_encode($result));

        // Fill data to Entity
        if (\is_array($result) && !empty($className)) {
            $result = EntityHelper::resultToEntity($result, $className);
        }

        return $result;
    }

    /**
     * @param bool $insert
     */
    public function setInsert(bool $insert)
    {
        $this->insert = $insert;
    }

    /**
     * @param bool $updateOrDelete
     */
    public function setUpdateOrDelete(bool $updateOrDelete)
    {
        $this->updateOrDelete = $updateOrDelete;
    }

    /**
     * @param bool $findOne
     */
    public function setFindOne(bool $findOne)
    {
        $this->findOne = $findOne;
    }

    /**
     * 转换结果
     *
     * @param mixed $result 查询结果
     *
     * @return mixed
     */
    private function transferResult($result)
    {
        if ($this->insert && $result !== false) {
            $result = $this->connection->getInsertId();
        } elseif ($this->updateOrDelete && $result !== false) {
            $result = $this->connection->getAffectedRows();
        } elseif ($this->findOne && $result !== false) {
            $result = $result[0] ?? [];
        }

        return $result;
    }
}