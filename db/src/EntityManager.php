<?php

namespace Swoft\Db;

use Swoft\Core\Coroutine;
use Swoft\Core\RequestContext;
use Swoft\Core\ResultInterface;
use Swoft\Db\Bean\Collector\EntityCollector;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Helper\DbHelper;
use Swoft\Helper\PoolHelper;
use Swoft\Pool\PoolInterface;

/**
 * Class EntityManager
 *
 * @package Swoft\Db
 */
class EntityManager implements EntityManagerInterface
{
    /**
     * Db connection
     *
     * @var \Swoft\Db\AbstractDbConnection
     */
    private $connection;

    /**
     * Connection pool
     *
     * @var PoolInterface
     */
    private $pool = null;

    /**
     * Is this EntityManager closed ?
     *
     * @var bool
     */
    private $isClose = false;

    /**
     * @var bool
     */
    private $isTransaction = false;

    /**
     * @var string
     */
    private $group;

    /**
     * EntityManager constructor.
     *
     * @param PoolInterface $pool
     * @param string        $group
     */
    private function __construct(PoolInterface $pool, string $group)
    {
        $this->pool       = $pool;
        $this->group      = $group;
        $this->connection = $pool->getConnection();
        $this->connection->setAutoRelease(false);
    }

    /**
     * Create a EntityManager
     *
     * @param string $group
     * @param string $node
     *
     * @return EntityManager
     */
    public static function create(string $group = Pool::GROUP, $node = Pool::MASTER): EntityManager
    {
        $pool = DbHelper::getPool($group, $node);

        return new EntityManager($pool, $group);
    }

    /**
     * Create a Query Builder
     *
     * @param string $sql
     *
     * @return QueryBuilder
     * @throws \Swoft\Db\Exception\DbException
     */
    public function createQuery(string $sql = ''): QueryBuilder
    {
        $this->checkStatus();
        $className = DbHelper::getQueryClassNameByConnection($this->connection);

        return new $className($this->group, $sql, null, $this->connection);
    }

    /**
     * Create a QueryBuild for ActiveRecord
     *
     * @param string $className Entity class name
     * @param string $group     Group id, master node will be used as defaults
     *
     * @return QueryBuilder
     */
    public static function getQuery(string $className, $group): QueryBuilder
    {
        $entities       = EntityCollector::getCollector();
        $tableName      = $entities[$className]['table']['name'];
        $queryClassName = DbHelper::getQueryClassNameByGroup($group);

        /* @var QueryBuilder $query */
        $query = new $queryClassName($group, '', $tableName);

        return $query;
    }

    /**
     * Save Entity
     *
     * @param object $entity
     *
     * @return ResultInterface
     * @throws \Swoft\Db\Exception\DbException
     */
    public function save($entity)
    {
        $this->checkStatus();
        $executor = $this->getExecutor();

        return $executor->save($entity);
    }

    /**
     * Delete Entity
     *
     * @param object $entity
     *
     * @return ResultInterface
     */
    public function delete($entity)
    {
        $this->checkStatus();
        $executor = $this->getExecutor();

        return $executor->delete($entity);
    }

    /**
     * @param $entity
     *
     * @return ResultInterface
     */
    public function update($entity): ResultInterface
    {
        $this->checkStatus();
        $executor = $this->getExecutor();

        return $executor->update($entity);
    }

    /**
     * Delete Entity by ID
     *
     * @param string $className Entity class nane
     * @param mixed  $id
     *
     * @return ResultInterface
     */
    public function deleteById($className, $id)
    {
        $this->checkStatus();
        $executor = $this->getExecutor();

        return $executor->deleteById($className, $id);
    }

    /**
     * Delete Entities by Ids
     *
     * @param string $className Entity class name
     * @param array  $ids       ID collection
     *
     * @return ResultInterface
     */
    public function deleteByIds($className, array $ids)
    {
        $this->checkStatus();
        $executor = $this->getExecutor();

        return $executor->deleteByIds($className, $ids);
    }

    /**
     * Find by Entity
     *
     * @param object $entity
     *
     * @return ResultInterface
     */
    public function find($entity): ResultInterface
    {
        $this->checkStatus();
        $executor = $this->getExecutor();

        return $executor->find($entity);
    }

    /**
     * Find Entity by ID
     *
     * @param string $className Entity class name
     * @param mixed  $id
     *
     * @return ResultInterface
     */
    public function findById($className, $id): ResultInterface
    {
        $this->checkStatus();
        $executor = $this->getExecutor();

        return $executor->findById($className, $id);
    }

    /**
     * Find Entites by IDs
     *
     * @param string $className transaction
     * @param array  $ids
     *
     * @return ResultInterface
     */
    public function findByIds($className, array $ids): ResultInterface
    {
        $this->checkStatus();
        $executor = $this->getExecutor();

        return $executor->findByIds($className, $ids);
    }

    /**
     * Begin transaction
     *
     * @throws \Swoft\Db\Exception\DbException
     */
    public function beginTransaction()
    {
        $this->checkStatus();
        $this->connection->beginTransaction();
        $this->beginTransactionContext();
        $this->isTransaction = true;
    }

    /**
     * Rollback transaction
     *
     * @throws \Swoft\Db\Exception\DbException
     */
    public function rollback()
    {
        $this->checkStatus();
        $this->connection->rollback();
        $this->isTransaction = false;
        $this->closetTransactionContext();
    }

    /**
     * Commit transaction
     *
     * @throws \Swoft\Db\Exception\DbException
     */
    public function commit()
    {
        $this->checkStatus();
        $this->connection->commit();
        $this->isTransaction = false;
        $this->closetTransactionContext();
    }

    /**
     * Close current EntityManager, and release the connection
     */
    public function close()
    {
        if ($this->isTransaction) {
            $this->rollback();
        }
        if (!$this->connection->isRecv()) {
            $this->connection->receive();
        }
        $this->isClose = true;
        $this->pool->release($this->connection);
    }

    /**
     * Check the EntityManager status
     *
     * @throws DbException
     */
    private function checkStatus()
    {
        if ($this->isClose) {
            throw new DbException('EntityManager was closed, no operation anymore');
        }
    }

    /**
     * Get an Executor
     *
     * @return Executor
     * @throws \Swoft\Db\Exception\DbException
     */
    private function getExecutor(): Executor
    {
        $query = $this->createQuery();

        return new Executor($query);
    }

    /**
     * Begin transaction context
     */
    private function beginTransactionContext()
    {
        $cntId        = $this->connection->getConnectionId();
        $contextTsKey = PoolHelper::getContextTsKey();
        $groupKey     = PoolHelper::getGroupKey($this->group);

        /* @var \SplStack $tsStack */
        $tsStack = RequestContext::getContextDataByChildKey($contextTsKey, $groupKey, new \SplStack());
        $tsStack->push($cntId);

        RequestContext::setContextDataByChildKey($contextTsKey, $groupKey, $tsStack);
    }

    /**
     * Close transaction context
     */
    private function closetTransactionContext()
    {
        $cid          = Coroutine::id();
        $contextTsKey = PoolHelper::getContextTsKey();
        $groupKey    = PoolHelper::getGroupKey($this->group);

        /* @var \SplStack $tsStack */
        $tsStack = RequestContext::getContextDataByChildKey($contextTsKey, $groupKey, new \SplStack());
        $tsStack->pop();

        RequestContext::setContextDataByChildKey($contextTsKey, $groupKey, $tsStack);
    }
}
