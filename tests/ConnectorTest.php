<?php

namespace Exonet\Api;

use Exonet\Api\Auth\PersonalAccessToken;
use Exonet\Api\Exceptions\AuthenticationException;
use Exonet\Api\Structures\ApiResource;
use Exonet\Api\Structures\ApiResourceSet;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ConnectorTest extends TestCase
{
    private $singleResource;

    private $multiResource;

    /*
     * Setup the the resources by using the setup method, because of the object typecasting.
     */
    public function setUp()
    {
        $this->singleResource = (object) [
            'data' => [
                'type' => 'unitTest',
                'id' => 'testId',
                'attributes' => [
                    'hello' => 'world',
                ],
                'relationships' => [
                    'testRelation' => [
                        'data' => ['id' => 'relationId', 'type' => 'relationType'],
                        'links' => ['self' => 'http://self.test', 'related' => 'http://related.test'],
                    ],
                ],
            ],
        ];

        $this->multiResource = [
            'data' => [
                [
                    'type' => 'unitTest',
                    'id' => 'testId',
                    'attributes' => [
                        'hello' => 'world',
                    ],
                    'relationships' => [
                        'testRelation' => [
                            'data' => ['id' => 'relationId', 'type' => 'relationType'],
                            'links' => ['self' => 'http://self.test', 'related' => 'http://related.test'],
                        ],
                    ],
                ],
            ],
            'meta' => [],
            'links' => [],
        ];
    }

    public function testGetSingleResourceWithValidResponse()
    {
        $apiCalls = [];
        $mock = new MockHandler([new Response(200, [], json_encode($this->singleResource))]);

        $history = Middleware::history($apiCalls);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        new Client(new PersonalAccessToken('test-token'));
        $connectorClass = new Connector($handler);

        $this->assertInstanceOf(ApiResource::class, $connectorClass->get('test'));

        $this->assertCount(1, $apiCalls);
        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $apiCalls[0]['request'];

        $this->assertSame('/test', $request->getUri()->getPath());
        $this->assertSame('Bearer test-token', $request->getHeader('Authorization')[0]);
        $this->assertSame('application/vnd.Exonet.v1+json', $request->getHeader('Accept')[0]);
        $this->assertSame('exonet-api-php/'.Client::CLIENT_VERSION, $request->getHeader('User-Agent')[0]);
    }

    public function testGetMultipleResourcesWithValidResponse()
    {
        $mock = new MockHandler([new Response(200, [], json_encode($this->multiResource))]);

        $handler = HandlerStack::create($mock);

        new Client(new PersonalAccessToken('test-token'));
        $connectorClass = new Connector($handler);

        $this->assertInstanceOf(ApiResourceSet::class, $connectorClass->get('test'));
    }

    public function testGetInvalidResponse()
    {
        $mock = new MockHandler([new Response(401)]);

        $handler = HandlerStack::create($mock);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Unauthenticated');

        new Client(new PersonalAccessToken('test-token'));
        $connectorClass = new Connector($handler);
        $connectorClass->get('test');
    }

    public function testPost()
    {
        $apiCalls = [];
        $mock = new MockHandler([new Response(201, [], json_encode($this->singleResource))]);

        $history = Middleware::history($apiCalls);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        new Client(new PersonalAccessToken('test-token'));
        $connectorClass = new Connector($handler);

        $payload = ['test' => 'demo'];

        $this->assertInstanceOf(ApiResource::class, $connectorClass->post('url', $payload));

        $this->assertCount(1, $apiCalls);
        /** @var \GuzzleHttp\Psr7\Request $request */
        $request = $apiCalls[0]['request'];

        $this->assertSame('/url', $request->getUri()->getPath());
        $this->assertSame('Bearer test-token', $request->getHeader('Authorization')[0]);
        $this->assertSame('application/vnd.Exonet.v1+json', $request->getHeader('Accept')[0]);
        $this->assertSame('exonet-api-php/'.Client::CLIENT_VERSION, $request->getHeader('User-Agent')[0]);
        $this->assertSame('application/json', $request->getHeader('Content-Type')[0]);
    }
}
