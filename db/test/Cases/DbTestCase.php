<?php

namespace Swoft\Db\Test\Cases;

use PHPUnit\Framework\TestCase;
use Swoft\Db\EntityManager;
use Swoft\Db\Pool;
use Swoft\Db\QueryBuilder;
use Swoft\Db\Test\Testing\Entity\User;
use Swoft\Db\Types;

/**
 * DbTestCache
 */
class DbTestCase extends TestCase
{
    public function arSave(string $group = Pool::GROUP)
    {
        $user = new User();
        $user->setName('stelin');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge(mt_rand(1, 100));

        $id     = $user->save($group)->getResult();
        $reuslt = $id > 0;
        $this->assertTrue($reuslt);
    }

    /**
     * @param int $id
     * @param string $group
     */
    public function arDelete(int $id, string $group = Pool::GROUP)
    {
        /* @var User $user */
        $user   = User::findById($id, $group)->getResult(User::class);
        $result = $user->delete($group)->getResult();
        $this->assertEquals(1, $result);
    }

    /**
     * @param int $id
     * @param string $group
     */
    public function arDeleteById(int $id, string $group = Pool::GROUP)
    {
        $result = User::deleteById($id, $group)->getResult();
        $this->assertEquals(1, $result);
    }

    /**
     * @param array $ids
     * @param string $group
     */
    public function arDeleteByIds(array $ids, string $group = Pool::GROUP)
    {
        $result = User::deleteByIds($ids, $group)->getResult();
        $this->assertEquals($result, 2);
    }

    /**
     * @param int $id
     * @param string $group
     */
    public function arUpdate(int $id, string $group = Pool::GROUP)
    {
        $newName = 'swoft framewrok';

        /* @var User $user */
        $user = User::findById($id, $group)->getResult(User::class);
        $user->setName($newName);
        $user->update($group)->getResult();

        /* @var User $newUser */
        $newUser = User::findById($id, $group)->getResult(User::class);
        $this->assertEquals($newName, $newUser->getName());
    }

    /**
     * @param int $id
     * @param string $group
     */
    public function arFindById(int $id, string $group = Pool::GROUP)
    {
        $user = User::findById($id, $group)->getResult();
        $this->assertEquals($id, $user['id']);
    }

    /**
     * @param int $id
     * @param string $group
     */
    public function arFindByIdClass(int $id, string $group = Pool::GROUP)
    {
        /* @var User $user */
        $user = User::findById($id, $group)->getResult(User::class);
        $this->assertEquals($id, $user->getId());
    }

    /**
     * @param array $ids
     * @param string $group
     */
    public function arFindByIds(array $ids, string $group = Pool::GROUP)
    {
        $users = User::findByIds($ids, $group)->getResult();

        $resultIds = [];
        foreach ($users as $user) {
            $resultIds[] = $user['id'];
        }
        $this->assertEquals(sort($resultIds), sort($ids));
    }

    /**
     * @param array  $ids
     * @param string $group
     */
    public function arFindByIdsByClass(array $ids, string $group = Pool::GROUP)
    {
        $users = User::findByIds($ids, $group)->getResult(User::class);

        $resultIds = [];
        /* @var User $user */
        foreach ($users as $user) {
            $resultIds[] = $user->getId();
        }
        $this->assertEquals(sort($resultIds), sort($ids));
    }

    public function arQuery(array $ids, string $group = Pool::GROUP)
    {
        $result = User::query($group)->select('*')->orderBy('id', QueryBuilder::ORDER_BY_DESC)->limit(2)->execute()->getResult();
        $this->assertCount(2, $result);
    }

    public function emSave(string $group = Pool::GROUP)
    {
        $user = new User();
        $user->setName('stelin');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge(mt_rand(1, 100));

        $em = EntityManager::create($group);
        $id = $em->save($user)->getResult();
        $em->close();

        $reuslt = $id > 0;
        $this->assertTrue($reuslt);
    }

    /**
     * @param int $id
     * @param string $group
     */
    public function emDelete(int $id, string $group = Pool::GROUP)
    {

        /* @var User $user */
        $user   = User::findById($id, $group)->getResult(User::class);
        $em = EntityManager::create($group);
        $result = $em->delete($user)->getResult();
        $em->close();

        $this->assertEquals(1, $result);
    }

    /**
     * @param int $id
     * @param string $group
     */
    public function emDeleteById(int $id, string $group = Pool::GROUP)
    {
        $em = EntityManager::create($group);
        $result = $em->deleteById(User::class, $id)->getResult();
        $em->close();

        $this->assertEquals(1, $result);
    }

    /**
     * @param array $ids
     * @param string $group
     */
    public function emDeleteByIds(array $ids, string $group = Pool::GROUP)
    {
        $em = EntityManager::create($group);
        $result = $em->deleteByIds(User::class, $ids)->getResult();
        $em->close();

        $this->assertEquals($result, 2);
    }

    /**
     * @param int $id
     * @param string $group
     */
    public function emUpdate(int $id, string $group = Pool::GROUP)
    {
        $newName = 'swoft framewrok';

        /* @var User $user */
        $user = User::findById($id, $group)->getResult(User::class);
        $user->setName($newName);
        $user->update($group)->getResult();

        $em = EntityManager::create($group);
        $em->update($user);
        $em->close();

        /* @var User $newUser */
        $newUser = User::findById($id, $group)->getResult(User::class);
        $this->assertEquals($newName, $newUser->getName());
    }

    /**
     * @param int    $id
     * @param string $group
     */
    public function emFindById(int $id, string $group = Pool::GROUP)
    {
        $em = EntityManager::create($group);
        $user = $em->findById(User::class, $id)->getResult();
        $em->close();

        $this->assertEquals($id, $user['id']);
    }

    /**
     * @param array $ids
     * @param string $group
     */
    public function emFindByIds(array $ids, string $group = Pool::GROUP)
    {
        $em = EntityManager::create($group);
        $users = $em->findByIds(User::class, $ids)->getResult();
        $em->close();

        $resultIds = [];
        foreach ($users as $user) {
            $resultIds[] = $user['id'];
        }
        $this->assertEquals(sort($resultIds), sort($ids));
    }


    public function emQuery(array $ids, string $group = Pool::GROUP)
    {
        $em = EntityManager::create($group);
        $result = $em->createQuery()->select('*')->from(User::class)->orderBy('id', QueryBuilder::ORDER_BY_DESC)->limit(2)->execute()->getResult();
        $em->close();

        $this->assertCount(2, $result);
    }

    public function emSql(array $ids, string $group = Pool::GROUP){
        $em = EntityManager::create($group);
        $result = $em->createQuery('select * from user where id in(?, ?) and name = ? order by id desc limit 2')
            ->setParameter(0, $ids[0])
            ->setParameter(1, $ids[1])
            ->setParameter(2, 'stelin')
            ->execute()->getResult();
        $em->close();

        $em = EntityManager::create($group);
        $result2 = $em->createQuery('select * from user where id in(?, ?) and name = ? order by id desc limit 2')
            ->setParameter(0, $ids[0])
            ->setParameter(1, $ids[1])
            ->setParameter(2, 'stelin', Types::STRING)
            ->execute()->getResult();
        $em->close();

        $em = EntityManager::create($group);
        $result3 = $em->createQuery('select * from user where id in(?, ?) and name = ? order by id desc limit 2')
            ->setParameters([$ids[0], $ids[1], 'stelin'])
            ->execute()->getResult();
        $em->close();

        $em = EntityManager::create($group);
        $result4 = $em->createQuery('select * from user where id in(:id1, :id2) and name = :name order by id desc limit 2')
            ->setParameter(':id1', $ids[0])
            ->setParameter('id2', $ids[1])
            ->setParameter('name', 'stelin')
            ->execute()->getResult();
        $em->close();

        $em = EntityManager::create($group);
        $result5 = $em->createQuery('select * from user where id in(:id1, :id2) and name = :name order by id desc limit 2')
            ->setParameters([
                'id1' => $ids[0],
                ':id2' => $ids[1],
                'name' => 'stelin'
            ])
            ->execute()->getResult();
        $em->close();


        $em = EntityManager::create($group);
        $result6 = $em->createQuery('select * from user where id in(:id1, :id2) and name = :name order by id desc limit 2')
            ->setParameters([
                ['id1', $ids[0]],
                [':id2', $ids[1], Types::INT],
                ['name', 'stelin', Types::STRING],
            ])
            ->execute()->getResult();
        $em->close();

        $this->assertCount(2, $result);
        $this->assertCount(2, $result2);
        $this->assertCount(2, $result3);
        $this->assertCount(2, $result4);
        $this->assertCount(2, $result5);
        $this->assertCount(2, $result6);
    }

    public function addUsers(string $group = Pool::GROUP)
    {
        $user = new User();
        $user->setName('stelin');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge(mt_rand(1, 100));
        $id  = $user->save($group)->getResult();
        $id2 = $user->save($group)->getResult();

        return [
            [[$id, $id2]],
        ];
    }

    public function addUser(string $group = Pool::GROUP)
    {
        $user = new User();
        $user->setName('stelin');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge(mt_rand(1, 100));
        $id = $user->save($group)->getResult();

        return [
            [$id],
        ];
    }

    public function mysqlProviders()
    {
        return $this->addUsers();
    }

    public function mysqlProvider()
    {
        return $this->addUser();
    }
}
