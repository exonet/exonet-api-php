<?php

namespace Exonet\Api\Structures;

use PHPUnit\Framework\TestCase;

class ApiResourceSetTest extends TestCase
{
    private $resourceSetData = [
        'data' => [
            [
                'type' => 'unitTest1',
                'id' => 'testId1',
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
            [
                'type' => 'unitTest2',
                'id' => 'testId2',
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
}
