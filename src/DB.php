<?php declare(strict_types=1);


namespace Swoft\Db;

use Swoft\Db\Exception\PoolException;
use Swoft\Db\Query\Builder;

/**
 * Class Db
 *
 * @see   Connection
 * @since 2.0
 *
 * @method static Builder table($table);
 */
class DB
{
    /**
     * Supported methods
     *
     * @var array
     */
    private static $passthru = [
        'table',
        'raw',
        'selectOne',
        'select',
        'cursor',
        'insert',
        'update',
        'delete',
        'statement',
        'affectingStatement',
        'unprepared',
        'prepareBindings',
        'transaction',
        'beginTransaction',
        'commit',
        'rollBack',
        'transactionLevel',
        'pretend',
    ];

    /**
     * @param string $name
     *
     * @return Connection
     * @throws PoolException
     */
    public static function pool(string $name = Pool::DEFAULT_POOL): Connection
    {
        try {
            $pool = \bean($name);
            if (!$pool instanceof Pool) {
                throw new PoolException(sprintf('%s is not instance of pool', $name));
            }

            return $pool->getConnection();
        } catch (\Throwable $e) {
            throw new PoolException(
                sprintf('Pool error is %s file=%s line=%d', $e->getMessage(), $e->getFile(), $e->getLine())
            );
        }
    }

    public static function __callStatic(string $name, array $arguments)
    {
        if (!in_array($name, self::$passthru)) {

        }

        $connection = self::pool();

        return $connection->$name(...$arguments);

        // TODO: Implement __callStatic() method.
    }
}