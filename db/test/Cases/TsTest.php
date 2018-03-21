<?php

namespace Swoft\Db\Test\Cases;

use Swoft\Db\Db;
use Swoft\Db\EntityManager;
use Swoft\Db\Test\Testing\Entity\User;

class TsTest extends DbTestCase
{
    public function testTsRollback()
    {
        $this->rollback();
        go(function () {
            $this->rollback();
        });
    }

    public function rollback()
    {
        $user = new User();
        $user->setName('stelin');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge(mt_rand(1, 100));

        $em = EntityManager::create();
        $em->beginTransaction();
        $uid  = $em->save($user)->getResult();
        $uid2 = $user->save()->getResult();
        $em->rollback();

        $user1 = User::findById($uid);
        $user2 = User::findById($uid2);

        $user1 = $user1->getResult();
        $user2 = $user2->getResult();

        $this->assertTrue(empty($user1));
        $this->assertTrue(empty($user2));
    }

    public function testTsCommit()
    {
        $this->commit();
        go(function () {
            $this->commit();
        });
    }

    public function commit()
    {
        $user = new User();
        $user->setName('stelin');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge(mt_rand(1, 100));

        $em = EntityManager::create();
        $em->beginTransaction();
        $uid  = $em->save($user)->getResult();
        $uid2 = $user->save()->getResult();
        $em->commit();

        $user1 = User::findById($uid);
        $user2 = User::findById($uid2);

        $user1Id = $user1->getResult();
        $user2Id = $user2->getResult();

        $this->assertEquals($uid, $user1Id['id']);
        $this->assertEquals($uid2, $user2Id['id']);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testDbBuilder(array $ids){
        $this->builder();

        go(function (){
            $this->builder();
        });
    }

    public function testNull()
    {
        $user = User::findById(12122223)->getResult(User::class);
        $this->assertEquals($user, null);
    }

    public function builder()
    {
        $result = Db::query('select * from user order by id desc limit 2')->execute()->getResult();
        $result2 = Db::query('select * from user order by id desc limit 2')->execute()->getResult(User::class);

        $result3 = Db::query()->select('*')->from(User::class)->where('name', 'stelin')->orderBy('id', 'DESC')->limit(1)->execute()->getResult();
        $result4 = Db::query()->select('*')
            ->from(User::class)
            ->where('name', 'stelin')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->execute()
            ->getResult(User::class);

        $this->assertCount(2, $result);
        $this->assertCount(2, $result2);
        $this->assertCount(5, $result3);
        $this->assertEquals('stelin', $result4->getName());
    }

}