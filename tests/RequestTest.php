<?php

namespace Exonet\Api;

use Exonet\Api\Structures\ApiResource;
use Exonet\Api\Structures\ApiResourceTest;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testGetRequest()
    {
        $response = new ApiResource(ApiResourceTest::SIMPLE_RESOURCE);

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
}
