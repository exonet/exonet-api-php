<?php

namespace Exonet\Api;

use Exonet\Api\Auth\PersonalAccessToken;
use Exonet\Api\Exceptions\AuthenticationException;
use Exonet\Api\Exceptions\ValidationException;
use Exonet\Api\Structures\ApiResource;
use Exonet\Api\Structures\ApiResourceSet;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ConnectorTest extends TestCase
{
    private $singleResource;

    private $multiResource;

    // Setup the the resources by using the setup method, because of the object typecasting.
    public function setUp(): void
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
            'links' => [
                'next' => null,
            ],
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
        $request = $apiCalls[0]['request'];

        $this->assertSame('api.exonet.nl', $request->getUri()->getHost());
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

    public function testGetRecursive()
    {
        $responseWithNext = $this->multiResource;
        $responseWithNext['links']['next'] = 'next.url';

        $mock = new MockHandler([
            new Response(200, [], json_encode($responseWithNext)),
            new Response(200, [], json_encode($this->multiResource)),
        ]);

        $handler = HandlerStack::create($mock);

        new Client(new PersonalAccessToken('test-token'));
        $connectorClass = new Connector($handler);

        $apiResourceSet = $connectorClass->getRecursive('test');
        $this->assertInstanceOf(ApiResourceSet::class, $apiResourceSet);
        $this->assertCount(2, $apiResourceSet);
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
        $request = $apiCalls[0]['request'];

        $this->assertSame('/url', $request->getUri()->getPath());
        $this->assertSame('Bearer test-token', $request->getHeader('Authorization')[0]);
        $this->assertSame('application/vnd.Exonet.v1+json', $request->getHeader('Accept')[0]);
        $this->assertSame('exonet-api-php/'.Client::CLIENT_VERSION, $request->getHeader('User-Agent')[0]);
        $this->assertSame('application/json', $request->getHeader('Content-Type')[0]);
    }

    public function testPatch()
    {
        $apiCalls = [];
        $mock = new MockHandler([new Response(201, [], json_encode($this->singleResource))]);

        $history = Middleware::history($apiCalls);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        new Client(new PersonalAccessToken('test-token'));
        $connectorClass = new Connector($handler);

        $payload = ['test' => 'demo'];

        $this->assertTrue($connectorClass->patch('url', $payload));

        $this->assertCount(1, $apiCalls);
        $request = $apiCalls[0]['request'];

        $this->assertSame('/url', $request->getUri()->getPath());
        $this->assertSame('Bearer test-token', $request->getHeader('Authorization')[0]);
        $this->assertSame('application/vnd.Exonet.v1+json', $request->getHeader('Accept')[0]);
        $this->assertSame('exonet-api-php/'.Client::CLIENT_VERSION, $request->getHeader('User-Agent')[0]);
        $this->assertSame('application/json', $request->getHeader('Content-Type')[0]);
    }

    public function testDelete()
    {
        $apiCalls = [];
        $mock = new MockHandler([new Response(204)]);

        $history = Middleware::history($apiCalls);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        new Client(new PersonalAccessToken('test-token'));
        $connectorClass = new Connector($handler);

        $result = $connectorClass->delete('url');

        $this->assertTrue($result);
        $this->assertCount(1, $apiCalls);
        $request = $apiCalls[0]['request'];

        $this->assertSame('/url', $request->getUri()->getPath());
        $this->assertSame('Bearer test-token', $request->getHeader('Authorization')[0]);
        $this->assertSame('application/vnd.Exonet.v1+json', $request->getHeader('Accept')[0]);
        $this->assertSame('exonet-api-php/'.Client::CLIENT_VERSION, $request->getHeader('User-Agent')[0]);
        $this->assertSame('application/json', $request->getHeader('Content-Type')[0]);
    }

    public function testInvalidPatch()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(422);

        $apiCalls = [];
        $mock = new MockHandler([
            new Response(
                422,
                [],
                '{"errors":[{"status":422,"code":"102.10001","title":"validation.generic","detail":"Validation did not pass.","variables":[]}]}'
            ),
        ]);

        $history = Middleware::history($apiCalls);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        new Client(new PersonalAccessToken('test-token'));
        $connectorClass = new Connector($handler);

        $payload = ['test' => 'demo'];
        $connectorClass->patch('url', $payload);
    }

    public function testInvalidDelete()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(422);

        $apiCalls = [];
        $mock = new MockHandler([
            new Response(
                422,
                [],
                '{"errors":[{"status":422,"code":"102.10001","title":"validation.generic","detail":"Validation did not pass.","variables":[]}]}'
            ),
        ]);

        $history = Middleware::history($apiCalls);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        new Client(new PersonalAccessToken('test-token'));
        $connectorClass = new Connector($handler);

        $payload = ['test' => 'demo'];
        $connectorClass->patch('url', $payload);
    }
}
