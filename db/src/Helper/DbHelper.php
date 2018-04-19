<?php

namespace Swoft\Db\Helper;

use Swoft\App;
use Swoft\Db\Bean\Collector\BuilderCollector;
use Swoft\Db\Exception\MysqlException;
use Swoft\Db\Pool;
use Swoft\Pool\ConnectionInterface;
use Swoft\Pool\PoolInterface;

/**
 * DbHelper
 */
class DbHelper
{
    /**
     * Delimiter
     */
    const GROUP_NODE_DELIMITER = '.';

    /**
     * @return string
     */
    public static function getContextSqlKey(): string
    {
        return 'swoft-sql';
    }

    /**
     * @param string $group
     * @param string $node
     *
     * @return \Swoft\Pool\PoolInterface
     */
    public static function getPool(string $group, string $node): PoolInterface
    {
        $poolName        = self::getPoolName($group, $node);
        $notConfig       = $node == Pool::SLAVE && !App::hasPool($poolName);
        $incorrectConfig = App::hasPool($poolName) && empty(App::getPool($poolName)->getPoolConfig()->getUri());

        if ($notConfig || $incorrectConfig) {
            $poolName = self::getPoolName($group, Pool::MASTER);
        }

        return App::getPool($poolName);
    }

    /**
     * @param string $group
     *
     * @throws \Swoft\Db\Exception\MysqlException
     * @return string
     */
    public static function getQueryClassNameByGroup(string $group):string
    {
        $pool = DbHelper::getPool($group, Pool::MASTER);
        /* @var \Swoft\Db\Pool\Config\DbPoolProperties $poolConfig*/
        $poolConfig = $pool->getPoolConfig();
        $driver = $poolConfig->getDriver();

        $collector = BuilderCollector::getCollector();
        if(!isset($collector[$driver])){
            throw new MysqlException(sprintf('The queryBuilder of %s is not exist!', $driver));
        }
        return $collector[$driver];
    }

    /**
     * Get the class name of QueryBuilder
     *
     * @param \Swoft\Pool\ConnectionInterface $connect
     *
     * @return string
     */
    public static function getQueryClassNameByConnection(ConnectionInterface $connect): string
    {
        $connectClassName = \get_class($connect);
        $classNameTmp     = str_replace('\\', '/', $connectClassName);
        $namespaceDir     = \dirname($classNameTmp);
        $namespace        = str_replace('/', '\\', $namespaceDir);
        $namespace        = sprintf('%s\\QueryBuilder', $namespace);

        return $namespace;
    }

    /**
     * @param string $group
     * @param string $node
     *
     * @return string
     */
    private static function getPoolName(string $group, string $node): string
    {
        $groupNode = explode(self::GROUP_NODE_DELIMITER, $group);
        if (count($groupNode) == 2) {
            return $group;
        }

        return sprintf('%s.%s', $group, $node);
    }
}