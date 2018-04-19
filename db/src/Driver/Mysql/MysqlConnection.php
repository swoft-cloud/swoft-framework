<?php

namespace Swoft\Db\Driver\Mysql;

use Swoft\App;
use Swoft\Db\Bean\Annotation\Connection;
use Swoft\Db\AbstractDbConnection;
use Swoft\Db\Exception\MysqlException;
use Swoole\Coroutine\Mysql;

/**
 * Mysql connection
 *
 * @Connection()
 */
class MysqlConnection extends AbstractDbConnection
{
    /**
     * @var Mysql
     */
    private $connection = null;

    /**
     * @var string
     */
    private $sql = '';

    /**
     * Prepare
     *
     * @param string $sql
     */
    public function prepare(string $sql)
    {
        $this->sql  = $sql;
    }

    /**
     * Execute
     *
     * @param array|null $params
     *
     * @return array|bool
     */
    public function execute(array $params = [])
    {
        $this->formatSqlByParams($params);
        $result = $this->connection->query($this->sql);
        if ($result === false) {
            App::error('Mysql execute error，connectError=' . $this->connection->connect_error . ' error=' . $this->connection->error);
        }

        $this->pushSqlToStack($this->sql);
        return $result;
    }

    /**
     * @return mixed
     */
    public function receive()
    {
        $result = $this->connection->recv();
        $this->connection->setDefer(false);
        $this->recv = true;

        return $result;
    }


    /**
     * @return mixed
     */
    public function getInsertId()
    {
        return $this->connection->insert_id;
    }

    /**
     * @return int
     */
    public function getAffectedRows(): int
    {
        return $this->connection->affected_rows;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        $this->connection->query('begin;');
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        if (!$this->recv) {
            throw new MysqlException('You forget to getResult() before rollback !');
        }
        $this->connection->query('rollback;');
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        if (!$this->recv) {
            throw new MysqlException('You forget to getResult() before commit !');
        }
        $this->connection->query('commit;');
    }

    /**
     * Create connection
     *
     * @throws \InvalidArgumentException
     */
    public function createConnection()
    {
        $uri                = $this->pool->getConnectionAddress();
        $options            = $this->parseUri($uri);
        $options['timeout'] = $this->pool->getTimeout();

        // init
        $mysql = new MySQL();
        $mysql->connect([
            'host'     => $options['host'],
            'port'     => $options['port'],
            'user'     => $options['user'],
            'password' => $options['password'],
            'database' => $options['database'],
            'timeout'  => $options['timeout'],
            'charset'  => $options['charset'],
        ]);

        // error
        if ($mysql->connected === false) {
            throw new MysqlException('Database connection error，error=' . $mysql->connect_error);
        }
        $this->connection = $mysql;
    }

    /**
     * @param bool $defer
     */
    public function setDefer($defer = true)
    {
        $this->recv = false;
        $this->connection->setDefer($defer);
    }


    /**
     * @return void
     */
    public function reconnect()
    {
        $this->createConnection();
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        return $this->connection->connected;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * Destory sql
     */
    public function destory()
    {
        $this->sql = '';
    }

    /**
     * 格式化sql参数
     *
     * @param array|null $params
     */
    private function formatSqlByParams(array $params = null)
    {
        if (empty($params)) {
            return;
        }

        foreach ($params as $key => &$value){
            $value = "'{$value}'";
        }

        // ?方式传递参数
        if (strpos($this->sql, '?') !== false) {
            $this->transferQuestionMark();
        }
        $this->sql = strtr($this->sql, $params);
    }
    /**
     * 格式化?标记
     */
    private function transferQuestionMark()
    {
        $sqlAry = explode('?', $this->sql);
        $sql = '';
        $maxBlock = \count($sqlAry);
        for ($i = 0; $i < $maxBlock; $i++) {
            $n = $i;
            $sql .= $sqlAry[$i];
            if ($maxBlock > $i + 1) {
                $sql .= '?' . $n . ' ';
            }
        }
        $this->sql = $sql;
    }
}
