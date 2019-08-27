<?php

namespace Exonet\Api;

use Exonet\Api\Structures\Resource;
use Exonet\Api\Structures\ResourceTest;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testGetRequest()
    {
        $response = new Resource('unitTest', ResourceTest::SIMPLE_RESOURCE);

        $connectorMock = Mockery::mock(Connector::class);
        $connectorMock
            ->shouldReceive('get')
            ->withArgs(['test?page%5Bsize%5D=1&page%5Bnumber%5D=2&filter%5Bunit%5D=test&filter%5Btest%5D=1&filter%5Bmulti%5D=a%2Cb%2Cc'])
            ->once()
            ->andReturn($response);

        $requestClass = new Request('/test', $connectorMock);

        $result = $requestClass->size(1)->page(2)->filter('unit', 'test')->filter('test')->filter('multi', ['a', 'b', 'c'])->get();

        $this->assertSame($response, $result);
    }

    public function testPostRequest()
    {
        $response = new Resource('unitTest', ResourceTest::SIMPLE_RESOURCE);

        $connectorMock = Mockery::mock(Connector::class);
        $connectorMock
            ->shouldReceive('post')
            ->withArgs(['test/', ['test' => 'something']])
            ->once()
            ->andReturn($response);

        $requestClass = new Request('/test', $connectorMock);

        $result = $requestClass->post(['test' => 'something']);

        $this->assertSame($response, $result);
    }

    public function testDeleteRequest()
    {
        $connectorMock = Mockery::mock(Connector::class);
        $connectorMock
            ->shouldReceive('delete')
            ->withArgs(['test/id999', []])
            ->once()
            ->andReturnNull();

        $request = new Request('/test', $connectorMock);

        $this->assertNull($request->delete('id999'));
    }
}
