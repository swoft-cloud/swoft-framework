<?php

namespace Swoft\Test\HttpClient;

use Swoft\App;
use Swoft\Core\Coroutine;
use Swoft\Http\Client;
use Swoft\Test\AbstractTestCase;
use Swoft\Testing\Base\Response;

/**
 * @uses      CoroutineClientTest
 * @version   2017-11-22
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CoroutineClientTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function get()
    {
        Coroutine::create(function () {
            $client = new Client();
            $client->setAdapter('coroutine');
            $method = 'GET';

            // Http
            /** @var Response $response */
            $response = $client->request($method, '', [
                'base_uri' => 'http://www.swoft.org',
            ])->getResponse();
            $response->assertSuccessful()->assertSee('Swoft 官网');

            // Https
            /** @var Response $response */
            $response = $client->request($method, '', [
                'base_uri' => 'https://www.swoft.org',
            ])->getResult();
            $response->assertStatus(302);

            // TODO add redirect HTTPS support

            /** @var Response $response */
            $response = $client->request($method, '/?a=1', [
                'base_uri' => 'http://echo.swoft.org',
            ])->getResult();
            $response->assertSuccessful();
            $this->assertJson($response->getBody()->getContents());
            $body = json_decode($response->getBody()->getContents(), true);
            $this->assertEquals('a=1', $body['uri']['query']);

            swoole_event_exit();
        });
    }

    /**
     * @test
     */
    public function postRawContent()
    {
        Coroutine::create(function () {
            $client = new Client();
            $client->setAdapter('coroutine');

            /**
             * Post raw body
             */
            $body = 'raw';
            $method = 'POST';
            /** @var Response $response */
            $response = $client->request($method, '', [
                'base_uri' => 'http://echo.swoft.org',
                'body' => $body,
            ])->getResult();
            $response->assertSuccessful()
                ->assertHeader('Content-Type', 'application/json')
                ->assertJsonFragment([
                    'Host' => 'echo.swoft.org',
                    'Content-Type' => 'text/plain',
                ])->assertJsonFragment([
                    'uri' => [
                        'scheme' => 'http',
                        'userInfo' => '',
                        'host' => 'echo.swoft.org',
                        'port' => 80,
                        'path' => '/',
                        'query' => '',
                        'fragment' => '',
                    ],
                ])->assertJsonFragment([
                    'method' => $method,
                    'body' => $body,
                ]);

            swoole_event_exit();
        });
    }

    /**
     * @test
     */
    public function postFormParams()
    {
        Coroutine::create(function () {
            $client = new Client();
            $client->setAdapter('coroutine');
            $body = [
                'string' => 'value',
                'int' => 1,
                'boolean' => true,
                'float' => 1.2345,
                'array' => [
                    '1',
                    '2',
                ],
            ];
            $method = 'POST';
            /** @var Response $response */
            $response = $client->request($method, '', [
                'base_uri' => 'http://echo.swoft.org',
                'form_params' => $body,
            ])->getResult();
            $response->assertSuccessful()
                ->assertHeader('Content-Type', 'application/json')
                ->assertJsonFragment([
                    'Host' => 'echo.swoft.org',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->assertJsonFragment([
                    'uri' => [
                        'scheme' => 'http',
                        'userInfo' => '',
                        'host' => 'echo.swoft.org',
                        'port' => 80,
                        'path' => '/',
                        'query' => '',
                        'fragment' => '',
                    ],
                ])->assertJsonFragment([
                    'method' => $method,
                    'body' => http_build_query($body),
                ]);

            swoole_event_exit();
        });
    }

    /**
     * @test
     */
    public function postJson()
    {
        Coroutine::create(function () {
            $client = new Client();
            $client->setAdapter('coroutine');
            $body = [
                'string' => 'value',
                'int' => 1,
                'boolean' => true,
                'float' => 1.2345,
                'array' => [
                    '1',
                    '2',
                ],
            ];
            $method = 'POST';
            /** @var Response $response */
            $response = $client->request($method, '', [
                'base_uri' => 'http://echo.swoft.org',
                'json' => $body,
            ])->getResult();
            $response->assertSuccessful()
                ->assertHeader('Content-Type', 'application/json')
                ->assertJsonFragment([
                    'Host' => 'echo.swoft.org',
                    'Content-Type' => 'application/json',
                ])->assertJsonFragment([
                    'uri' => [
                        'scheme' => 'http',
                        'userInfo' => '',
                        'host' => 'echo.swoft.org',
                        'port' => 80,
                        'path' => '/',
                        'query' => '',
                        'fragment' => '',
                    ],
                ])->assertJsonFragment([
                    'method' => $method,
                    'body' => json_encode($body),
                ]);

            swoole_event_exit();
        });
    }

    /**
     * @test
     */
    public function putJson()
    {
        Coroutine::create(function () {
            $client = new Client();
            $client->setAdapter('coroutine');
            $body = [
                'string' => 'value',
                'int' => 1,
                'boolean' => true,
                'float' => 1.2345,
                'array' => [
                    '1',
                    '2',
                ],
            ];
            $method = 'PUT';
            /** @var Response $response */
            $response = $client->request($method, '', [
                'base_uri' => 'http://echo.swoft.org',
                'json' => $body,
            ])->getResult();
            $response->assertSuccessful()
                ->assertHeader('Content-Type', 'application/json')
                ->assertJsonFragment([
                    'Host' => 'echo.swoft.org',
                    'Content-Type' => 'application/json',
                ])->assertJsonFragment([
                    'uri' => [
                        'scheme' => 'http',
                        'userInfo' => '',
                        'host' => 'echo.swoft.org',
                        'port' => 80,
                        'path' => '/',
                        'query' => '',
                        'fragment' => '',
                    ],
                ])->assertJsonFragment([
                    'method' => $method,
                    'body' => json_encode($body),
                ]);

            swoole_event_exit();
        });
    }

    /**
     * @test
     */
    public function deleteJson()
    {
        Coroutine::create(function () {
            $client = new Client();
            $client->setAdapter('coroutine');
            $body = [
                'string' => 'value',
                'int' => 1,
                'boolean' => true,
                'float' => 1.2345,
                'array' => [
                    '1',
                    '2',
                ],
            ];
            $method = 'DELETE';
            /** @var Response $response */
            $response = $client->request($method, '', [
                'base_uri' => 'http://echo.swoft.org',
                'json' => $body,
            ])->getResult();
            $response->assertSuccessful()
                ->assertHeader('Content-Type', 'application/json')
                ->assertJsonFragment([
                    'Host' => 'echo.swoft.org',
                    'Content-Type' => 'application/json',
                ])->assertJsonFragment([
                    'uri' => [
                        'scheme' => 'http',
                        'userInfo' => '',
                        'host' => 'echo.swoft.org',
                        'port' => 80,
                        'path' => '/',
                        'query' => '',
                        'fragment' => '',
                    ],
                ])->assertJsonFragment([
                    'method' => $method,
                    'body' => json_encode($body),
                ]);

            swoole_event_exit();
        });
    }

    /**
     * @test
     */
    public function batch()
    {
        Coroutine::create(function () {
            $client = new Client([
                'base_uri' => 'http://www.swoft.org',
            ]);
            $client->setAdapter('coroutine');
            $request1 = $client->request('GET', '');
            $request2 = $client->request('GET', '');
            $request3 = $client->request('GET', '');

            /** @var Response $response1 */
            $response1 = $request1->getResponse();
            /** @var Response $response2 */
            $response2 = $request2->getResponse();
            /** @var Response $response3 */
            $response3 = $request3->getResponse();

            $response1->assertSuccessful()->assertSee('Swoft 官网');
            $response2->assertSuccessful()->assertSee('Swoft 官网');
            $response3->assertSuccessful()->assertSee('Swoft 官网');

            swoole_event_exit();
        });
    }

    /**
     * @test
     */
    public function defaultUserAgent()
    {
        Coroutine::create(function () {
            $client = new Client();
            $client->setAdapter('coroutine');
            $expected = sprintf('Swoft/%s PHP/%s', App::version(), PHP_VERSION);
            $this->assertEquals($expected, $client->getDefaultUserAgent());

            swoole_event_exit();
        });
    }
}
