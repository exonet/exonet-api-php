<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

class ApiResourceIdentifierTest extends TestCase
{
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

    public function testDelete()
    {
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('delete')
            ->once()
            ->withArgs(['xV42'])
            ->andReturnNull();

        $resourceIdentifier = new ApiResourceIdentifier('unitTest', 'xV42', $requestMock);

        $this->assertNull($resourceIdentifier->delete());
    }
}
