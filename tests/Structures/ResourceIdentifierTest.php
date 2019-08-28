<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Request;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ResourceIdentifierTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testTypeAndId()
    {
        $resource = new ResourceIdentifier('unitTest', 'id4');

        $this->assertSame('unitTest', $resource->type());
        $this->assertSame('id4', $resource->id());
    }

    public function testGet()
    {
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('get')
            ->once()
            ->withArgs(['xV42'])
            ->andReturn(new Resource('unitTest'));

        $resourceIdentifier = new ResourceIdentifier('unitTest', 'xV42', $requestMock);

        $this->assertInstanceOf(Resource::class, $resourceIdentifier->get());
    }

    public function testDeleteResource()
    {
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('delete')
            ->once()
            ->withArgs(['xV42'])
            ->andReturnNull();

        $resourceIdentifier = new ResourceIdentifier('unitTest', 'xV42', $requestMock);

        $this->assertNull($resourceIdentifier->delete());
    }

    public function testDeleteRelation()
    {
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('delete')
            ->once()
            ->withArgs(['xV42/relationships/test', ['data' => ['type' => 'testRelation', 'id' => 'testId']]])
            ->andReturnNull();

        $requestMock->shouldReceive('delete')
            ->once()
            ->withArgs(['xV42/relationships/test2', ['data' => [['type' => 'testRelation2', 'id' => 'testId2']]]])
            ->andReturnNull();

        $resourceIdentifier = new ResourceIdentifier('unitTest', 'xV42', $requestMock);
        $resourceIdentifier->relationship('test', new ResourceIdentifier('testRelation', 'testId'));
        $resourceIdentifier->relationship('test2', [new ResourceIdentifier('testRelation2', 'testId2')]);

        $this->assertNull($resourceIdentifier->delete());
    }
}
