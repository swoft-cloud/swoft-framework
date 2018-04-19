<?php

namespace Swoft\Db;

/**
 * Interface EntityManagerInterface
 *
 * @package Swoft\Db
 */
interface EntityManagerInterface
{
    /**
     * Create a EntityManager instance
     *
     * @param string $poolId
     * @return EntityManager
     */
    public static function create(string $poolId): EntityManager;


    /**
     * Rollback transaction
     */
    public function rollback();

    /**
     * Begin transaction
     */
    public function beginTransaction();

    /**
     * Commit transaction
     */
    public function commit();


    /**
     * Create a Query Builder
     *
     * @param string $sql
     * @return QueryBuilder
     */
    public function createQuery(string $sql = ''): QueryBuilder;
}
