<?php

namespace Exonet\Api\Exceptions;

use Exonet\Api\Client;
use GuzzleHttp\Psr7\Response as PsrResponse;

/**
 * Try handling Response exceptions to throw a sensible exception.
 */
class ResponseExceptionHandler
{
    /**
     * @var PsrResponse The response to handle.
     */
    private $response;

    /**
     * @var bool|string The response body.
     */
    private $responseBody;

    /**
     * @var string[] List of error codes and the exception to throw.
     */
    private $errorCodes = [
        '101' => InvalidRequestException::class,
        '102' => ValidationException::class,
        '103' => AuthorizationException::class,
        '104' => NotFoundException::class,
        '501' => UnknownException::class,
    ];

    /**
     * @var Client The Client instance.
     */
    private $client;

    /**
     * ResponseExceptionHandler constructor.
     *
     * @param PsrResponse $response The response.
     */
    public function __construct(PsrResponse $response, ?Client $client = null)
    {
        $this->response = $response;
        $this->responseBody = $response->getBody()->getContents();
        $this->client = $client ?? Client::getInstance();
    }

    /**
     * Handle the response and try to throw a sensible exception.
     *
     * @throws ExonetApiException An (extended) instance of ExonetApiException.
     */
    public function handle() : void
    {
        $this->client->log()->error(
            'Request failed',
            ['statusCode' => $this->response->getStatusCode(), 'contents' => $this->responseBody]
        );

        // First, try to catch known exceptions based on status code.
        if ($this->response->getStatusCode() === 401) {
            throw new AuthenticationException('Unauthenticated');
        } elseif ($this->response->getStatusCode() === 404) {
            throw new NotFoundException();
        }

        /*
         * If nothing is thrown yet, try parsing the response to get an exception. If nothing was found, throw an
         * unknown exception.
         */
        throw $this->parseResponse() ?? new UnknownException('There was an unknown exception.');
    }

    /**
     * Try parsing the errors returned from the API to determine a sensible exception. Every exception is an extension
     * of ExonetApiException.
     *
     * @return ExonetApiException|null The error class or null if no class can be determined.
     */
    private function parseResponse() : ?ExonetApiException
    {
        // If there are validation errors, parse them separately to include all failed validations.
        if ($this->response->getStatusCode() === 422) {
            return $this->parseValidationErrors();
        }

        $error = json_decode($this->responseBody, true)['errors'][0] ?? null;

        if (!$error) {
            return null;
        }

        $errorCode = substr($error['code'], 0, 3);
        if (isset($this->errorCodes[$errorCode])) {
            return new $this->errorCodes[$errorCode](
                $error['detail'] ?? null,
                $error['status'] ?? 0,
                null,
                $error['code'] ?? null,
                $error['variables'] ?? []
            );
        }

        return null;
    }

    /**
     * Parse the validation errors to a single exception, but with details for each failed validation rule.
     *
     * @return ExonetApiException|null The validation exception with details.
     */
    private function parseValidationErrors() : ?ExonetApiException
    {
        $errorList = json_decode($this->responseBody, true)['errors'] ?? null;
        $errorCount = count($errorList);

        // Return if no errors are found.
        if ($errorList === null || $errorCount === 0) {
            return null;
        }

        // Create the exception.
        $exceptionMessage = $errorCount === 1 ? 'There is %d validation error.' : 'There are %d validation errors.';
        $exception = new ValidationException(sprintf($exceptionMessage, $errorCount), 422, null, '102.10001');

        // Add each failed validation error to the exception.
        foreach ($errorList as $error) {
            $exception->setFailedValidation($error['variables']['field'] ?? null, $error['detail']);
        }

        return $exception;
    }
}
