<?php

namespace Swoft\Db\Test\Cases;

use Swoft\Db\Test\Testing\Entity\User;

/**
 * BugMysqlTest
 */
class BugMysqlTest extends DbTestCase
{
    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testResult(int $id)
    {
        $this->query($id);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testCo(int $id)
    {
        go(function () use ($id) {
            $this->query($id);
        });
    }

    public function query($id)
    {
        $query = User::query()->select('name,id')->where('id', $id)->limit(1)->execute();

        $result = $query->getResult();
        $this->assertCount(2, $result);
        $this->assertFalse(empty(get_last_sql()));
    }

    public function testAttrs()
    {
        $this->attr();
    }

    public function testCoAttr()
    {
        go(function (){
            $this->attr();
        });
    }

    public function attr()
    {
        $attrs = [
            'name' => 'stelin3',
            'sex'  => 1,
            'desc' => 'this is my desc2',
            'age'  => 99,
        ];
        $user  = new User();
        $user->fill($attrs);
        $result = $user->save()->getResult();

        /* @var User $user */
        $user = User::findById($result)->getResult(User::class);

        $this->assertEquals($user->getName(), 'stelin3');
        $this->assertEquals($user->getSex(), 1);
        $this->assertEquals($user->getDesc(), 'this is my desc2');
        $this->assertEquals($user->getAge(), 99);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testModelSelect(int $id)
    {
        $this->modelSelect($id);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testModelCoSelect(int $id)
    {
        go(function () use ($id){
            $this->modelSelect($id);
        });
    }

    public function modelSelect($id)
    {
        $result = User::query()->select('*')->where('id', $id)->limit(1)->execute()->getResult();
        $this->assertEquals($id, (int)$result['id']);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testModelDelete(int $id)
    {
        $this->modelDelete($id);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testModelCoDelete(int $id)
    {
        go(function () use ($id){
            $this->modelDelete($id);
        });
    }

    public function modelDelete(int $id)
    {
        $result = User::query()->delete()->where('id', $id)->limit(1)->execute()->getResult();
        $this->assertEquals(1, $result);

        $user = User::findById($id)->getResult();
        $this->assertEmpty($user);
    }


    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testModelUpdate(int $id)
    {
        $this->modelUpdate($id);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testModelCoUpdate(int $id)
    {
        go(function () use ($id){
            $this->modelUpdate($id);
        });
    }

    public function modelUpdate(int $id)
    {
        $data = [
            'name' => 'stelin7872',
            'sex'  => 18,
            'description' => 'descc',
            'age'  => 100,
        ];

        $result = User::query()->update()->set($data)->where('id', $id)->execute()->getResult();
        $this->assertEquals(1, $result);

        /* @var User $user*/
        $user = User::findById($id)->getResult(User::class);
        $this->assertEquals($user->getName(), 'stelin7872');
        $this->assertEquals($user->getSex(), 18);
        $this->assertEquals($user->getDesc(), 'descc');
        $this->assertEquals($user->getAge(), 100);
    }

    /**
     */
    public function testModelInsert()
    {
        $this->modelInsert();
    }

    /**
     */
    public function testModelCoInsert()
    {
        go(function (){
            $this->modelInsert();
        });
    }

    /**
     */
    public function modelInsert()
    {
        $data = [
            'name' => 'stelin666',
            'sex'  => 19,
            'description' => '1212',
            'age'  => 100,
        ];

        $id = User::query()->insert()->set($data)->execute()->getResult();
        $this->assertFalse(empty($id));

        /* @var User $user*/
        $user = User::findById($id)->getResult(User::class);
        $this->assertEquals($user->getName(), 'stelin666');
        $this->assertEquals($user->getSex(), 19);
        $this->assertEquals($user->getDesc(), '1212');
        $this->assertEquals($user->getAge(), 100);
    }
}