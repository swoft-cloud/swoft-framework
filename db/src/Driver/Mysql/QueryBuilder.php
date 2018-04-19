<?php

namespace Swoft\Db\Driver\Mysql;

use Swoft\App;
use Swoft\Core\ResultInterface;
use Swoft\Db\AbstractDbConnection;
use Swoft\Db\Bean\Annotation\Builder;
use Swoft\Db\DbCoResult;
use Swoft\Db\DbDataResult;
use Swoft\Db\Helper\EntityHelper;
use Swoft\Helper\JsonHelper;

/**
 * Mysql query builder
 *
 * @Builder()
 */
class QueryBuilder extends \Swoft\Db\QueryBuilder
{
    /**
     * @var string
     */
    private $profilePrefix = 'mysql';

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (App::isCoContext()) {
            return $this->getCorResult();
        }

        return $this->getSyncResult();
    }

    /**
     * @return DbDataResult
     */
    private function getSyncResult()
    {
        $sql = $this->getStatement();
        list($sqlId, $profileKey) = $this->getSqlIdAndProfileKey($sql);

        App::profileStart($profileKey);

        /* @var AbstractDbConnection $connection*/
        $connection = $this->selectConnection();
        $connection->prepare($sql);
        $result = $connection->execute($this->parameters);

        App::profileEnd($profileKey);
        App::debug(sprintf('sql execute sqlId=%s, result=%s, sql=%s', $sqlId, JsonHelper::encode($result, JSON_UNESCAPED_UNICODE), $sql));

        $isFindOne = isset($this->limit['limit']) && $this->limit['limit'] === 1;
        if ($this->isInsert()) {
            $result = $connection->getInsertId();
        } elseif ($this->isUpdate() || $this->isDelete()) {
            $result = $connection->getAffectedRows();
        } else {
            $result = $connection->fetch();
        }

        $result = $this->transferResult($connection, $result);

        if (is_array($result) && !empty($className)) {
            $result = EntityHelper::resultToEntity($result, $className);
        }
        $syncData = new DbDataResult($result, $connection);

        return $syncData;
    }

    /**
     * @return ResultInterface
     */
    private function getCorResult()
    {
        $sql = $this->getStatement();
        list($sqlId, $profileKey) = $this->getSqlIdAndProfileKey($sql);

        /* @var AbstractDbConnection $connection*/
        $connection = $this->selectConnection();
        $connection->setDefer();
        $connection->prepare($sql);
        $result = $connection->execute($this->parameters);

        App::debug(sprintf('sql execute sqlId=%s, sql=%s', $sqlId, $sql));
        $isUpdateOrDelete = $this->isDelete() || $this->isUpdate();
        $isFindOne        = $this->isSelect() && isset($this->limit['limit']) && $this->limit['limit'] === 1;
        $corResult        = new DbCoResult($connection, $profileKey);

        // 结果转换参数
        $corResult->setInsert($this->isInsert());
        $corResult->setUpdateOrDelete($isUpdateOrDelete);
        $corResult->setFindOne($isFindOne);

        return $corResult;
    }

    /**
     * @param string $sql
     *
     * @return array
     */
    private function getSqlIdAndProfileKey(string $sql)
    {
        $sqlId      = md5($sql);
        $profileKey = sprintf('%s.%s', $sqlId, $this->profilePrefix);

        return [$sqlId, $profileKey];
    }

    /**
     * 转换结果
     *
     * @param AbstractDbConnection $connection
     * @param mixed                $result
     *
     * @return mixed
     */
    private function transferResult(AbstractDbConnection $connection, $result)
    {
        $isFindOne        = isset($this->limit['limit']) && $this->limit['limit'] === 1;
        $isUpdateOrDelete = $this->isDelete() || $this->isUpdate();
        if ($result !== false && $this->isInsert()) {
            $result = $connection->getInsertId();
        } elseif ($result !== false && $isUpdateOrDelete) {
            $result = $connection->getAffectedRows();
        } elseif ($isFindOne && $result !== false && $this->isSelect()) {
            $result = $result[0] ?? [];
        }

        return $result;
    }

    /**
     * @param mixed $key
     *
     * @return string
     */
    protected function formatParamsKey($key): string
    {
        if (\is_string($key) && strpos($key, ':') === false) {
            return ':' . $key;
        }
        if (is_int($key) && App::isCoContext()) {
            return '?' . $key;
        }

        return $key;
    }
}
