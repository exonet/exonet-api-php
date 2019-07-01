<?php

namespace Exonet\Api\Exceptions;

use Exonet\Api\Client;
use GuzzleHttp\Psr7\Response;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ResponseExceptionHandlerTest extends TestCase
{
    public function testHandle401Statuscode()
    {
        $logMock = Mockery::mock(LoggerInterface::class);
        $logMock->shouldReceive('error')->withArgs(['Request failed', ['statusCode' => 401, 'contents' => '{"content":"Unauthorized"}']])->once();

        $client = new Client();
        $client->setLogger($logMock);

        $response = new Response(401, [], json_encode(['content' => 'Unauthorized']));

        $this->expectExceptionMessage('Unauthenticated');
        $this->expectException(AuthenticationException::class);

        $exceptionHandler = new ResponseExceptionHandler($response, $client);
        $exceptionHandler->handle();
    }

    public function testHandleInvalidRequest()
    {
        $logMock = Mockery::mock(LoggerInterface::class);
        $logMock->shouldReceive('error')->withArgs(['Request failed', ['statusCode' => 400, 'contents' => '{"errors":[{"code":"101.10001","detail":"Invalid Request Test"}]}']])->once();

        $client = new Client();
        $client->setLogger($logMock);

        $response = new Response(400, [], json_encode(['errors' => [['code' => '101.10001', 'detail' => 'Invalid Request Test']]]));

        $this->expectExceptionMessage('Invalid Request Test');
        $this->expectException(InvalidRequestException::class);

        $exceptionHandler = new ResponseExceptionHandler($response, $client);
        $exceptionHandler->handle();
    }

    public function testHandleValidationException()
    {
        $logMock = Mockery::mock(LoggerInterface::class);
        $logMock->shouldReceive('error')->withArgs(['Request failed', ['statusCode' => 422, 'contents' => '{"errors":[{"code":"102.10001","detail":"Validation Exception Test"}]}']])->once();

        $client = new Client();
        $client->setLogger($logMock);

        $response = new Response(422, [], json_encode(['errors' => [['code' => '102.10001', 'detail' => 'Validation Exception Test']]]));

        $this->expectExceptionMessage('Validation Exception Test');
        $this->expectException(ValidationException::class);

        $exceptionHandler = new ResponseExceptionHandler($response, $client);
        $exceptionHandler->handle();
    }

    public function testHandleAuthorizationException()
    {
        $logMock = Mockery::mock(LoggerInterface::class);
        $logMock->shouldReceive('error')->withArgs(['Request failed', ['statusCode' => 403, 'contents' => '{"errors":[{"code":"103.10001","detail":"Authorization Exception Test"}]}']])->once();

        $client = new Client();
        $client->setLogger($logMock);

        $response = new Response(403, [], json_encode(['errors' => [['code' => '103.10001', 'detail' => 'Authorization Exception Test']]]));

        $this->expectExceptionMessage('Authorization Exception Test');
        $this->expectException(AuthorizationException::class);

        $exceptionHandler = new ResponseExceptionHandler($response, $client);
        $exceptionHandler->handle();
    }

    public function testHandle_FromContent_NotFoundException()
    {
        // Status code is not 404, but error message code refers to a not found exception.
        $logMock = Mockery::mock(LoggerInterface::class);
        $logMock->shouldReceive('error')->withArgs(['Request failed', ['statusCode' => 400, 'contents' => '{"errors":[{"code":"104.10001","detail":"NotFound Exception Test"}]}']])->once();

        $client = new Client();
        $client->setLogger($logMock);

        $response = new Response(400, [], json_encode(['errors' => [['code' => '104.10001', 'detail' => 'NotFound Exception Test']]]));

        $this->expectExceptionMessage('NotFound Exception Test');
        $this->expectException(NotFoundException::class);

        $exceptionHandler = new ResponseExceptionHandler($response, $client);
        $exceptionHandler->handle();
    }

    public function testHandle_FromStatusCode_NotFoundException()
    {
        $logMock = Mockery::mock(LoggerInterface::class);
        $logMock->shouldReceive('error')->withArgs(['Request failed', ['statusCode' => 404, 'contents' => '[]']])->once();

        $client = new Client();
        $client->setLogger($logMock);

        $response = new Response(404, [], json_encode([]));

        $this->expectExceptionMessage('');
        $this->expectException(NotFoundException::class);

        $exceptionHandler = new ResponseExceptionHandler($response, $client);
        $exceptionHandler->handle();
    }

    public function testHandleUnknownException()
    {
        $logMock = Mockery::mock(LoggerInterface::class);
        $logMock->shouldReceive('error')->withArgs(['Request failed', ['statusCode' => 500, 'contents' => '{"errors":[{"code":"501.10001","detail":"Unknown Exception Test"}]}']])->once();

        $client = new Client();
        $client->setLogger($logMock);

        $response = new Response(500, [], json_encode(['errors' => [['code' => '501.10001', 'detail' => 'Unknown Exception Test']]]));

        $this->expectExceptionMessage('Unknown Exception Test');
        $this->expectException(UnknownException::class);

        $exceptionHandler = new ResponseExceptionHandler($response, $client);
        $exceptionHandler->handle();
    }

    public function testHandleNoError()
    {
        $logMock = Mockery::mock(LoggerInterface::class);
        $logMock->shouldReceive('error')->withArgs(['Request failed', ['statusCode' => 500, 'contents' => '']])->once();

        $client = new Client();
        $client->setLogger($logMock);

        $response = new Response(500, [], '');

        $this->expectExceptionMessage('There was an unknown exception.');
        $this->expectException(UnknownException::class);

        $exceptionHandler = new ResponseExceptionHandler($response, $client);
        $exceptionHandler->handle();
    }

    public function testHandleNoKnownError()
    {
        $logMock = Mockery::mock(LoggerInterface::class);
        $logMock->shouldReceive('error')->withArgs(['Request failed', ['statusCode' => 500, 'contents' => '{"errors":[{"code":"???.10001","detail":"Unknown Code Exception Test"}]}']])->once();

        $client = new Client();
        $client->setLogger($logMock);

        $response = new Response(500, [], json_encode(['errors' => [['code' => '???.10001', 'detail' => 'Unknown Code Exception Test']]]));

        $this->expectExceptionMessage('There was an unknown exception.');
        $this->expectException(UnknownException::class);

        $exceptionHandler = new ResponseExceptionHandler($response, $client);
        $exceptionHandler->handle();
    }
}
