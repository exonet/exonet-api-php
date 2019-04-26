<?php

namespace Exonet\Api\Structures;

use PHPUnit\Framework\TestCase;

class ApiResourceTest extends TestCase
{
    public const SIMPLE_RESOURCE = [
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
    ];

    public function testMagicGetSet()
    {
        $resourceClass = new ApiResource(self::SIMPLE_RESOURCE);

        $this->assertSame('unitTest', $resourceClass->resourceType);
        $this->assertSame('testId', $resourceClass->id);
        $this->assertSame('world', $resourceClass->hello);

        $resourceClass->hello = 'bye';
        $this->assertSame('bye', $resourceClass->hello);
    }

    public function testIsset()
    {
        $resourceClass = new ApiResource(self::SIMPLE_RESOURCE);

        $this->assertTrue(isset($resourceClass->hello));

        // Test that if the attribute has a null value, 'false' is returned.
        $resourceClass->hello = null;
        $this->assertFalse(isset($resourceClass->hello));
    }

    public function testCall()
    {
        $resourceClass = new ApiResource(self::SIMPLE_RESOURCE);

        $this->assertInstanceOf(Relation::class, $resourceClass->testRelation());
    }

    public function testArrayAccessMethods()
    {
        $resourceClass = new ApiResource(self::SIMPLE_RESOURCE);

        $this->assertSame('unitTest', $resourceClass['resourceType']);
        $this->assertSame('testId', $resourceClass['id']);
        $this->assertSame('world', $resourceClass['hello']);

        $resourceClass['hello'] = 'bye';
        $resourceClass['id'] = 'newId';

        $this->assertSame('bye', $resourceClass['hello']);
        $this->assertSame('newId', $resourceClass['id']);

        $this->assertTrue(isset($resourceClass['hello']));

        // Test that if the attribute has a null value, 'false' is returned.
        $resourceClass['hello'] = null;
        $this->assertFalse(isset($resourceClass['hello']));

        // Unsetting a value completely removes it from the attribute list.
        unset($resourceClass['hello']);
        $this->assertFalse(isset($resourceClass['hello']));
    }
}
