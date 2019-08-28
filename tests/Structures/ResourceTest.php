<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Request;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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

    public function testGetSetAttribute()
    {
        $resourceClass = new Resource('unitTest', self::SIMPLE_RESOURCE);

        $this->assertSame('world', $resourceClass->attribute('hello'));

        $resourceClass->attribute('hello', 'bye');
        $this->assertSame('bye', $resourceClass->attribute('hello'));
    }

    public function testRelated()
    {
        $resourceClass = new Resource('unitTest', self::SIMPLE_RESOURCE);

        $this->assertInstanceOf(Relation::class, $resourceClass->related('testRelation'));
    }

    public function testPostNewResource()
    {
        $postData = [
            'data' => [
                'type' => 'unitTest',
                'attributes' => ['hello' => 'world'],
                'relationships' => ['testRelation' => ['data' => ['type' => 'relationType', 'id' => 'relationId']]]
            ]
        ];
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('post')->with($postData)->once()->andReturn(self::SIMPLE_RESOURCE);

        $resource = new Resource('unitTest', [], $request);
        $resource->attribute('hello', 'world');
        $resource->relationship('testRelation', new ResourceIdentifier('relationType', 'relationId'));
        $this->assertSame(self::SIMPLE_RESOURCE, $resource->post());
    }

    public function testPostChangedRelation()
    {
        $postData = [
            'data' => ['type' => 'relationType', 'id' => 'relationId2']
        ];
        $request = Mockery::mock(Request::class);
        $request
            ->shouldReceive('post')
            ->with($postData, 'testId/relationships/testRelation')
            ->once()
            ->andReturn(self::SIMPLE_RESOURCE['relationships']['testRelation']);

        $resource = new Resource('unitTest', self::SIMPLE_RESOURCE, $request);
        $resource->relationship('testRelation', new ResourceIdentifier('relationType', 'relationId2'));
        $this->assertArrayHasKey('testRelation', $resource->post());
    }

    public function testPatch()
    {
        $patchAttribute = ['data' => ['type' => 'unitTest', 'id' => 'abc', 'attributes' => ['another' => 'attribute']]];
        $patchRelation = ['data' => ['type' => 'relationType', 'id' => 'relationId']];
        $patchMultiRelation = ['data' => [['type' => 'relationType1', 'id' => 'relationId1'],['type' => 'relationType2', 'id' => 'relationId2']]];
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('patch')->with('abc', $patchAttribute)->once()->andReturnTrue();
        $request->shouldReceive('patch')->with('abc/relationships/testRelation', $patchRelation)->once()->andReturnTrue();
        $request->shouldReceive('patch')->with('abc/relationships/multiRelation', $patchMultiRelation)->once()->andReturnTrue();

        $resource = new Resource('unitTest', 'abc', $request);
        $resource->attribute('another', 'attribute');
        $resource->relationship('testRelation', new ResourceIdentifier('relationType', 'relationId'));
        $resource->relationship('multiRelation', [new ResourceIdentifier('relationType1', 'relationId1'), new ResourceIdentifier('relationType2', 'relationId2')]);
        $this->assertTrue($resource->patch());
    }
}
