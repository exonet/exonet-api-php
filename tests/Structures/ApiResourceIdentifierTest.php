<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Request;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ApiResourceIdentifierTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testTypeAndId()
    {
        $resource = new ApiResourceIdentifier('unitTest', 'id4');

        $this->assertSame('unitTest', $resource->type());
        $this->assertSame('id4', $resource->id());
    }

    public function testGet()
    {
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('get')
            ->once()
            ->withArgs(['xV42'])
            ->andReturn(new ApiResource('unitTest'));

        $resourceIdentifier = new ApiResourceIdentifier('unitTest', 'xV42', $requestMock);

        $this->assertInstanceOf(ApiResource::class, $resourceIdentifier->get());
    }

    public function testDeleteResource()
    {
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('delete')
            ->once()
            ->withArgs(['xV42'])
            ->andReturnTrue();

        $resourceIdentifier = new ApiResourceIdentifier('unitTest', 'xV42', $requestMock);

        $this->assertTrue($resourceIdentifier->delete());
    }

    public function testDeleteRelation()
    {
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('delete')
            ->once()
            ->withArgs(['xV42/relationships/test', ['data' => ['type' => 'testRelation', 'id' => 'testId']]])
            ->andReturnTrue();

        $requestMock->shouldReceive('delete')
            ->once()
            ->withArgs(['xV42/relationships/test2', ['data' => [['type' => 'testRelation2', 'id' => 'testId2']]]])
            ->andReturnTrue();

        $resourceIdentifier = new ApiResourceIdentifier('unitTest', 'xV42', $requestMock);
        $resourceIdentifier->relationship('test', new ApiResourceIdentifier('testRelation', 'testId'));
        $resourceIdentifier->relationship('test2', [new ApiResourceIdentifier('testRelation2', 'testId2')]);

        $this->assertTrue($resourceIdentifier->delete());
    }
}
