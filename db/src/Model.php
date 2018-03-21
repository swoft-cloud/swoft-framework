<?php

namespace Swoft\Db;

use Swoft\Core\ResultInterface;

/**
 * The model of activerecord
 */
class Model
{
    /**
     * The data of old
     *
     * @var array
     */
    private $attrs = [];

    /**
     * Insert data to db
     *
     * @param string $group
     *
     * @return ResultInterface
     */
    public function save(string $group = Pool::GROUP)
    {
        $executor = self::getExecutor($group);

        return $executor->save($this);
    }

    /**
     * Delete data from db
     *
     * @param string $group
     *
     * @return ResultInterface
     */
    public function delete(string $group = Pool::GROUP)
    {
        $executor = self::getExecutor($group);

        return $executor->delete($this);
    }

    /**
     * Delete data by id
     *
     * @param mixed  $id ID
     * @param string $group
     *
     * @return ResultInterface
     */
    public static function deleteById($id, string $group = Pool::GROUP)
    {
        $executor = self::getExecutor($group);

        return $executor->deleteById(static::class, $id);
    }

    /**
     * Delete by ids
     *
     * @param array  $ids
     * @param string $group
     *
     * @return ResultInterface
     */
    public static function deleteByIds(array $ids, string $group = Pool::GROUP)
    {
        $executor = self::getExecutor($group);

        return $executor->deleteByIds(static::class, $ids);
    }

    /**
     * Update data
     *
     * @param string $group
     *
     * @return ResultInterface
     */
    public function update(string $group = Pool::GROUP)
    {
        $executor = self::getExecutor($group);

        return $executor->update($this);
    }

    /**
     * Find data from db
     *
     * @param string $group
     *
     * @return ResultInterface
     */
    public function find(string $group = Pool::GROUP)
    {
        $executor = self::getExecutor($group);

        return $executor->find($this);
    }

    /**
     * Find by id
     *
     * @param mixed  $id
     * @param string $group
     *
     * @return ResultInterface
     */
    public static function findById($id, string $group = Pool::GROUP)
    {
        $executor = self::getExecutor($group);

        return $executor->findById(static::class, $id);
    }

    /**
     * Find by ids
     *
     * @param array  $ids
     * @param string $group
     *
     * @return ResultInterface
     */
    public static function findByIds(array $ids, string $group = Pool::GROUP)
    {
        $executor = self::getExecutor($group);

        return $executor->findByIds(static::class, $ids);
    }

    /**
     * Get the QueryBuilder
     *
     * @param string $group
     *
     * @return QueryBuilder
     */
    public static function query(string $group = Pool::GROUP): QueryBuilder
    {
        return EntityManager::getQuery(static::class, $group);
    }


    /**
     * Get the exeutor
     *
     * @param string $group
     *
     * @return Executor
     */
    private static function getExecutor(string $group = Pool::GROUP): Executor
    {
        $queryBuilder = EntityManager::getQuery(static::class, $group);
        $executor     = new Executor($queryBuilder, $group);

        return $executor;
    }

    /**
     * @return array
     */
    public function getAttrs(): array
    {
        return $this->attrs;
    }

    /**
     * @param array $attrs
     */
    public function setAttrs(array $attrs)
    {
        $this->attrs = $attrs;
    }

    /**
     * @param array $attributes
     *
     * $attributes = [
     *     'name' => $value
     * ]
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $methodName = sprintf('set%s', ucfirst($name));
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }
    }
}
