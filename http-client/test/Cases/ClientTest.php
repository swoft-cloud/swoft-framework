<?php

namespace SwoftTest\HttpClient;

use Swoft\HttpClient\Client;


/**
 * @uses      ClientTest
 * @version   2018年02月26日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2018 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ClientTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function result()
    {
        $request = function () {
            $client = new Client();
            return $client->request('GET', '', [
                'base_uri' => 'http://echo.swoft.org',
            ]);
        };
        $this->assertEquals($request()->getResponse()->getBody()->getContents(), $request()->getResult());
    }

}