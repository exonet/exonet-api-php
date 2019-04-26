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
            reset($resources)['hello']
        );

        // Test isset with an offset that should not exist.
        $this->assertFalse(isset($resourceSetClass[55]));

        // Set a new offset.
        $resourceSetClass[55] = new ApiResource([]);

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
}
