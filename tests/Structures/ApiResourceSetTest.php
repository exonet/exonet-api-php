<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Auth\PersonalAccessToken;
use Exonet\Api\Client;
use Exonet\Api\Connector;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ApiResourceSetTest extends TestCase
{
    private $resourceSetData = [
        'data' => [
            [
                'type' => 'unitTest1',
                'id' => 'testId1',
                'attributes' => [
                    'hello' => 'world1',
                ],
                'relationships' => [
                    'testRelation' => [
                        'data' => ['id' => 'relationId', 'type' => 'relationType'],
                        'links' => ['self' => 'http://self.test', 'related' => 'http://related.test'],
                    ],
                ],
            ],
            [
                'type' => 'unitTest2',
                'id' => 'testId2',
                'attributes' => [
                    'hello' => 'world2',
                ],
                'relationships' => [
                    'testRelation' => [
                        'data' => ['id' => 'relationId', 'type' => 'relationType'],
                        'links' => ['self' => 'http://self.test', 'related' => 'http://related.test'],
                    ],
                ],
            ],
        ],
        'meta' => [
            'count' => 2,
        ],
        'links' => [
            'self' => 'http://set.test',
        ],
    ];

    public function testGetIterator()
    {
        $resourceSetClass = new ApiResourceSet(json_encode($this->resourceSetData));

        foreach ($resourceSetClass as $resource) {
            $this->assertInstanceOf(ApiResource::class, $resource);
        }
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testGetItratorWithNoData()
    {
        $resourceSetClass = new ApiResourceSet(json_encode([
            'data' => [],
            'meta' => ['count' => 0],
            'links' => ['self' => 'http://set.test'],
        ]));

        foreach ($resourceSetClass as $resource) {
            // Iterating with no data should not hit this lines and also throw no errors.
            $this->fail();
        }
    }

    public function testArrayAccessMethods()
    {
        $resourceSetClass = new ApiResourceSet(json_encode($this->resourceSetData));
        $resources = $resourceSetClass->getIterator();

        $this->assertSame(
            'world1',
            $resources[0]->attribute('hello')
        );

        // Test isset with an offset that should not exist.
        $this->assertFalse(isset($resourceSetClass[55]));

        // Set a new offset.
        $resourceSetClass[55] = new ApiResource(
            'some_resource',
            [
                'id' => 'ABC',
                'type' => 'some_resource',
                'attributes' => [],
            ]
        );

        // Test isset with an offset that should now exist.
        $this->assertTrue(isset($resourceSetClass[55]));

        // Unsetting a value completely removes it from the attribute list.
        unset($resourceSetClass[1]);
        $this->assertFalse(isset($resourceSetClass[1]));
    }

    public function testOffsetSetValidation()
    {
        $resourceSetClass = new ApiResourceSet(json_encode($this->resourceSetData));

        $this->expectException(\Exonet\Api\Exceptions\ValidationException::class);
        $this->expectExceptionMessage('Only ApiResources can be set.');

        // Try to set something other than an ApiResource.
        $resourceSetClass[55] = 'some string';
    }

    public function testPaginationLinks()
    {
        Client::getInstance()->setAuth(new PersonalAccessToken('test-token'));
        $apiUrl = Client::getInstance()->getApiUrl();
        $data = json_encode(
            [
                'data' => [],
                'meta' => ['count' => 0],
                'links' => [
                    'next' => $apiUrl.'next.url',
                    'prev' => $apiUrl.'previous.url',
                    'first' => $apiUrl.'first.url',
                    'last' => $apiUrl.'last.url',
                ],
            ]
        );

        $apiCalls = [];
        $mock = new MockHandler(
            [
                new Response(200, [], $data),
                new Response(200, [], $data),
                new Response(200, [], $data),
                new Response(200, [], $data),
            ]
        );

        $history = Middleware::history($apiCalls);
        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $resourceSet = new ApiResourceSet($data, new Connector($handler));
        $this->assertInstanceOf(ApiResourceSet::class, $resourceSet->nextPage());

        $resourceSet = new ApiResourceSet($data, new Connector($handler));
        $this->assertInstanceOf(ApiResourceSet::class, $resourceSet->previousPage());

        $resourceSet = new ApiResourceSet($data, new Connector($handler));
        $this->assertInstanceOf(ApiResourceSet::class, $resourceSet->firstPage());

        $resourceSet = new ApiResourceSet($data, new Connector($handler));
        $this->assertInstanceOf(ApiResourceSet::class, $resourceSet->lastPage());

        // Test the called URLs.
        $this->assertSame('/next.url', $apiCalls[0]['request']->getRequestTarget());
        $this->assertSame('/previous.url', $apiCalls[1]['request']->getRequestTarget());
        $this->assertSame('/first.url', $apiCalls[2]['request']->getRequestTarget());
        $this->assertSame('/last.url', $apiCalls[3]['request']->getRequestTarget());
    }
}
