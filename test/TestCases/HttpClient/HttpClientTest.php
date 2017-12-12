<?php

namespace Swoft\Test\HttpClient;

use Swoft\App;
use Swoft\Http\Client;
use Swoft\Test\AbstractTestCase;
use Swoft\Testing\Base\Response;


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
        // Http
        /** @var Response $response */
        $response = $client->request('GET', '', [
            'base_uri' => 'http://www.baidu.com',
        ])->getResponse();
        $response->assertSuccessful()->assertSee('百度一下，你就知道');

        // Https
        /** @var Response $response */
        $response = $client->request('GET', '', [
            'base_uri' => 'https://www.baidu.com',
        ])->getResult();
        $response->assertSuccessful()->assertSee('百度一下，你就知道');

        // Http 302 -> Https
        /** @var Response $response */
        $response = $client->request('GET', '', [
            'base_uri' => 'http://www.swoft.org',
        ])->getResult();
        $response->assertStatus(302);
    }

    /**
     * @test
     * @requires extension curl
     */
    public function defaultUserAgent()
    {
        $client = new Client();
        $expected = sprintf('Swoft/%s curl/%s PHP/%s', App::version(), \curl_version()['version'], PHP_VERSION);
        $this->assertEquals($expected, $client->getDefaultUserAgent());
    }

}