<?php

namespace Swoft\Db;

use Swoft\App;
use Swoft\Bean\BeanFactory;
use Swoft\Core\ResultInterface;
use Swoft\Db\Bean\Collector\EntityCollector;
use Swoft\Db\Validator\ValidatorInterface;
use Swoft\Exception\ValidatorException;

/**
 * The executor of db
 */
class Executor
{
    /**
     * 查询器
     *
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * Executor constructor.
     *
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * insert实体数据
     *
     * @param object $entity 实体
     *
     * @return ResultInterface
     */
    public function save($entity): ResultInterface
    {
        // 实体映射信息处理
        list($table, , , $fields) = $this->getFields($entity, 1);

        // 构建insert查询器
        $this->queryBuilder->insert($table);
        foreach ($fields ?? [] as $column => $value) {
            $this->queryBuilder->set($column, $value);
        }
        return $this->getResult();
    }

    /**
     * 按实体信息删除数据
     *
     * @param object $entity 实体
     *
     * @return ResultInterface
     */
    public function delete($entity): ResultInterface
    {
        // 实体映射数据
        list($table, , , $fields) = $this->getFields($entity, 3);

        // 构建delete查询器
        $this->queryBuilder->delete()->from($table);
        foreach ($fields ?? [] as $column => $value) {
            $this->queryBuilder->where($column, $value);
        }

        return $this->getResult();
    }

    /**
     * 根据ID删除数据
     *
     * @param string $className 实体类名
     * @param mixed  $id        删除ID
     *
     * @return ResultInterface
     */
    public function deleteById($className, $id): ResultInterface
    {
        // 实体映射数据
        list($table, , $idColumn) = $this->getTable($className);

        // 构建delete查询器
        $this->queryBuilder->delete()->from($table)->where($idColumn, $id);
        return $this->getResult();
    }

    /**
     * 根据ID删除数据
     *
     * @param string $className 实体类名
     * @param array  $ids       ID集合
     *
     * @return ResultInterface
     */
    public function deleteByIds($className, array $ids): ResultInterface
    {
        // 实体映射数据
        list($table, , $idColumn) = $this->getTable($className);

        // 构建delete查询器
        $this->queryBuilder->delete()->from($table)->whereIn($idColumn, $ids);
        return $this->getResult();
    }

    /**
     * 按实体更新信息(默认按照主键)
     *
     * @param object $entity 具体实体实例
     *
     * @return ResultInterface
     */
    public function update($entity): ResultInterface
    {
        // 实体映射数据
        list($table, $idColumn, $idValue, $fields) = $this->getFields($entity, 2);

        if (empty($fields)) {
            $pool = $this->queryBuilder->getConnection();
            $connection = $this->queryBuilder->getConnection();
            return new DbDataResult(0, $connection);
        }
        // 构建update查询器
        $this->queryBuilder->update($table)->where($idColumn, $idValue);
        foreach ($fields ?? [] as $column => $value) {
            $this->queryBuilder->set($column, $value);
        }
        return $this->getResult();
    }

    /**
     * 按实体信息查找
     *
     * @param object $entity 实体实例
     *
     * @return ResultInterface
     */
    public function find($entity): ResultInterface
    {
        // 实体映射数据
        list($tableName, , , $fields) = $this->getFields($entity, 3);

        // 构建find查询器
        $this->queryBuilder->select('*')->from($tableName);
        foreach ($fields ?? [] as $column => $value) {
            $this->queryBuilder->where($column, $value);
        }
        return $this->queryBuilder->execute();
    }

    /**
     * 根据ID查找
     *
     * @param string $className 实体类名
     * @param mixed  $id        ID
     *
     * @return ResultInterface
     */
    public function findById($className, $id): ResultInterface
    {
        // 实体映射数据
        list($tableName, , $columnId) = $this->getTable($className);

        // 构建find查询器
        $query = $this->queryBuilder->select("*")->from($tableName)->where($columnId, $id)->limit(1);
        return $query->execute();
    }

    /**
     * 根据ids查找
     *
     * @param string $className 类名
     * @param array  $ids
     *
     * @return ResultInterface
     */
    public function findByIds($className, array $ids): ResultInterface
    {
        // 实体映射数据
        list($tableName, , $columnId) = $this->getTable($className);

        // 构建find查询器
        $query = $this->queryBuilder->select("*")->from($tableName)->whereIn($columnId, $ids);
        return $query->execute();
    }

    /**
     * 获取实体映射结构
     *
     * @param object $entity 实体对象
     * @param int    $type   类型，1=insert 3=delete|find 2=update
     *
     * @return array
     * @throws \Swoft\Exception\ValidatorException
     */
    private function getFields($entity, $type = 1): array
    {
        $changeFields = [];

        // 实体表结构信息
        list($table, $id, $idColumn, $fields) = $this->getClassMetaData($entity);

        // 实体映射字段、值处理以及验证处理
        $idValue = null;
        foreach ($fields as $proName => $proAry) {
            $column  = $proAry['column'];
            $default = $proAry['default'];

            // 实体属性对应值
            $proValue = $this->getEntityProValue($entity, $proName);

            // insert逻辑
            if ($type === 1 && $id === $proName && $default === $proValue) {
                continue;
            }

            // update逻辑
            if ($type === 2 && null === $proValue) {
                continue;
            }

            // delete和find逻辑
            if ($type === 3 && $default === $proValue) {
                continue;
            }

            // 属性值验证
            $this->validate($proAry, $proValue);

            // id值赋值
            if ($idColumn === $column) {
                $idValue = $proValue;
            }

            $changeFields[$column] = $proValue;
        }

        // 如果是更新找到变化的字段
        if ($type === 2) {
            $oldFields    = $entity->getAttrs();
            $changeFields = array_diff($changeFields, $oldFields);
        }

        return [$table, $idColumn, $idValue, $changeFields];
    }

    /**
     * 属性值验证
     *
     * @param array $columnAry     属性字段验证规则
     * @param mixed $propertyValue 数组字段值
     *
     * @throws ValidatorException
     */
    private function validate(array $columnAry, $propertyValue)
    {
        // 验证信息
        $column    = $columnAry['column'];
        $length    = $columnAry['length'] ?? -1;
        $validates = $columnAry['validates'] ?? [];
        $type      = $columnAry['type'] ?? Types::STRING;
        $required  = $columnAry['required'] ?? false;

        // 必须传值验证
        if ($propertyValue === null && $required) {
            throw new ValidatorException('数据字段验证失败，column=' . $column . '字段必须设置值');
        }

        // 类型验证器
        $validator = [
            'name'  => ucfirst($type),
            'value' => [$length]
        ];

        // 所有验证器
        array_unshift($validates, $validator);

        // 循环验证，一个验证不通过，验证失败
        foreach ($validates as $validate) {
            $name     = $validate['name'];
            $params   = $validate['value'];
            $beanName = 'Validator' . $name;

            // 验证器未定义
            if (!BeanFactory::hasBean($beanName)) {
                App::warning('验证器不存在，beanName=' . $beanName);
                continue;
            }

            /* @var ValidatorInterface $objValidator */
            $objValidator = App::getBean($beanName);
            $objValidator->validate($column, $propertyValue, $params);
        }
    }

    /**
     * 实体属性对应的值
     *
     * @param object $entity  实体对象
     * @param string $proName 属性名称
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function getEntityProValue($entity, string $proName)
    {
        $proName      = explode('_', $proName);
        $proName      = array_map(function ($word) {
            return ucfirst($word);
        }, $proName);
        $proName      = implode('', $proName);
        $getterMethod = 'get' . $proName;
        if (!method_exists($entity, $getterMethod)) {
            throw new \InvalidArgumentException('实体对象属性getter方法不存在，properName=' . $proName);
        }
        $proValue = $entity->$getterMethod();

        return $proValue;
    }

    /**
     * 实例映射信息
     *
     * @param object $entity
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    private function getClassMetaData($entity): array
    {
        // 不是对象
        if (!\is_object($entity) && !class_exists($entity)) {
            throw new \InvalidArgumentException('实体不是对象');
        }

        // 对象实例不是实体
        $entities  = EntityCollector::getCollector();
        $className = \is_string($entity) ? $entity : \get_class($entity);
        if (!isset($entities[$className]['table']['name'])) {
            throw new \InvalidArgumentException('对象不是实体对象，className=' . $className);
        }

        return $this->getTable($className);
    }

    /**
     * 实体表映射结构
     *
     * @param string $className
     *
     * @return array
     */
    private function getTable(string $className): array
    {
        $entities   = EntityCollector::getCollector();
        $fields     = $entities[$className]['field'];
        $idProperty = $entities[$className]['table']['id'];
        $tableName  = $entities[$className]['table']['name'];
        $idColumn   = $entities[$className]['column'][$idProperty];
        return [$tableName, $idProperty, $idColumn, $fields];
    }

    /**
     * 获取执行结果
     *
     * @return ResultInterface
     */
    private function getResult()
    {
        return $this->queryBuilder->execute();
    }
}
