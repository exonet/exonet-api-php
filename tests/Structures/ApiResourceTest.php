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

    public function testResourceIdentifier()
    {
        $resourceClass = new ApiResource('unitTest', self::SIMPLE_RESOURCE);

        $this->assertSame('unitTest', $resourceClass->type());
        $this->assertSame('testId', $resourceClass->id());
    }

    public function testGetSetAttribute()
    {
        $resourceClass = new ApiResource('unitTest', self::SIMPLE_RESOURCE);

        $this->assertSame('world', $resourceClass->attribute('hello'));

        $resourceClass->attribute('hello', 'bye');
        $this->assertSame('bye', $resourceClass->attribute('hello'));
    }

    public function testRelated()
    {
        $resourceClass = new ApiResource('unitTest', self::SIMPLE_RESOURCE);

        $this->assertInstanceOf(Relation::class, $resourceClass->related('testRelation'));
    }
}
