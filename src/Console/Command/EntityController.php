<?php

namespace Swoft\Console\Command;

use Swoft\Console\ConsoleController;
use \Swoft\Db\Entity\Generator;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Bean\BeanFactory;
use Swoft\App;
use Swoft\Db\Entity\Mysql\Schema;
use Swoft\Pool\DbSlavePool;

/**
 * the group command list of database entity
 *
 * @uses      EntityController
 * @version   2017年10月11日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class EntityController extends ConsoleController
{
    /**
     * @var array $drivers 数据库驱动列表
     */
    private $drivers = ['Mysql'];

    /**
     * @var \Swoft\Db\Entity\Schema $schema schema对象
     */
    private $schema;

    /**
     * @var Generator $generatorEntity 实体实例
     */
    private $generatorEntity;

    /**
     * @var string $filePath 实体文件路径
     */
    private $filePath = '@app/Models/Entity';

    /**
     * 初始化
     *
     * @param Input  $input  输入
     * @param Output $output 输出
     */
    public function __construct(Input $input, Output $output)
    {
        parent::__construct($input, $output);
    }

    /**
     * Auto create entity by table structure
     *
     * @usage
     * entity:create -d[|--database] <database>
     * entity:create -d[|--database] <database> [table]
     * entity:create -d[|--database] <database> -i[|--include] <table>
     * entity:create -d[|--database] <database> -i[|--include] <table1,table2>
     * entity:create -d[|--database] <database> -i[|--include] <table1,table2> -e[|--exclude] <table3>
     * entity:create -d[|--database] <database> -i[|--include] <table1,table2> -e[|--exclude] <table3,table4>
     *
     * @options
     * -d  数据库
     * --database  数据库
     * -i  指定特定的数据表，多表之间用逗号分隔
     * --include  指定特定的数据表，多表之间用逗号分隔
     * -e  排除指定的数据表，多表之间用逗号分隔
     * --exclude  排除指定的数据表，多表之间用逗号分隔
     *
     * @example
     * php bin/swoft entity:create -d test
     */
    public function createCommand()
    {
        $this->init();

        $database = '';
        $tablesEnabled = $tablesDisabled = [];

        $this->parseDatabaseCommand($database);
        $this->parseEnableTablesCommand($tablesEnabled);
        $this->parseDisableTablesCommand($tablesDisabled);

        if (empty($database)) {
            $this->output->writeln('databases doesn\'t not empty!');
        } else {
            $this->generatorEntity->db = $database;
            $this->generatorEntity->tablesEnabled = $tablesEnabled;
            $this->generatorEntity->tablesDisabled = $tablesDisabled;
            $this->generatorEntity->execute($this->schema);
        }
    }

    /**
     * 初始化方法
     */
    private function init()
    {
        App::setAlias('@entityPath', $this->filePath);
        $pool = App::getBean(DbSlavePool::class);
        $driver = $pool->getDriver();
        if (in_array($driver, $this->drivers)) {
           $schema = new Schema();
           $schema->setDriver($driver);
           $this->schema = $schema;
        } else {
            throw new \RuntimeException('There is no corresponding driver matching schema');
        }
        $syncDbConnect = $pool->createConnect();
        $this->generatorEntity = new Generator($syncDbConnect);
    }

    /**
     * 解析需要扫描的数据库
     *
     * @param string &$database 需要扫描的数据库
     */
    private function parseDatabaseCommand(string &$database)
    {
        if ($this->input->hasSOpt('d') || $this->input->hasLOpt('database')) {
            $database = $this->input->hasSOpt('d') ? $this->input->getShortOpt('d') : $this->input->getLongOpt('database');
        }
    }

    /**
     * 解析需要扫描的table
     *
     * @param array &$tablesEnabled 需要扫描的表
     */
    private function parseEnableTablesCommand(&$tablesEnabled)
    {
        if ($this->input->hasSOpt('i') || $this->input->hasLOpt('include')) {
            $tablesEnabled = $this->input->hasSOpt('i') ? $this->input->getShortOpt('i') : $this->input->getLongOpt('include');
            $tablesEnabled = !empty($tablesEnabled) ? explode(',', $tablesEnabled) : [];
        }

        // 参数优先级大于选项
        if (!empty($this->input->getArg(0))) {
            $tablesEnabled = [$this->input->getArg(0)];
        }
    }

    /**
     * 解析不需要扫描的table
     *
     * @param array &$tablesDisabled 不需要扫描的表
     */
    private function parseDisableTablesCommand(&$tablesDisabled)
    {
        if ($this->input->hasSOpt('e') || $this->input->hasLOpt('exclude')) {
            $tablesDisabled = $this->input->hasSOpt('e') ? $this->input->getShortOpt('e') : $this->input->getLongOpt('exclude');
            $tablesDisabled = !empty($tablesDisabled) ? explode(',', $tablesDisabled) : [];
        }
    }
}
