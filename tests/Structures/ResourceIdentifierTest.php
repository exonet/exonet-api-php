<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

class ResourceIdentifierTest extends TestCase
{
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

    public function testDelete()
    {
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('delete')
            ->once()
            ->withArgs(['xV42'])
            ->andReturnNull();

        $resourceIdentifier = new ResourceIdentifier('unitTest', 'xV42', $requestMock);

        $this->assertNull($resourceIdentifier->delete());
    }
}
