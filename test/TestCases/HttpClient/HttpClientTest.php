<?php

namespace Swoft\Test\HttpClient;

use Swoft\Http\Client;
use Swoft\I18n\I18n;
use Swoft\Test\AbstractTestCase;


/**
 * @uses      HttpClientTest
 * @version   2017-11-22
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class HttpClientTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function get()
    {
        $client = new Client();
        $response = $client->request('GET', '', [
            'base_uri' => 'www.baidu.com',
        ]);
        $this->assertTrue(true);
    }

}