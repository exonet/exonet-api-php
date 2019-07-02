<?php

declare(strict_types=1);

namespace Exonet\Api;

use Exonet\Api\Exceptions\ResponseExceptionHandler;
use Exonet\Api\Structures\ApiResource;
use Exonet\Api\Structures\ApiResourceIdentifier;
use Exonet\Api\Structures\ApiResourceSet;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as PsrResponse;

/**
 * This class is responsible for making the calls to the Exonet API and returning the retrieved data as ApiResource or
 * ApiResourceSet.
 */
class Connector
{
    /**
     * @var GuzzleClient The Guzzle client.
     */
    private $httpClient;

    /**
     * @var Client The API client.
     */
    private $apiClient;

    /**
     * Connector constructor.
     *
     * @param HandlerStack|null $guzzleHandlerStack Optional Guzzle handlers.
     * @param Client|null       $client             The client instance.
     */
    public function __construct(?HandlerStack $guzzleHandlerStack = null, ?Client $client = null)
    {
        // Don't let Guzzle throw exceptions, as it is handled by this class.
        $this->httpClient = new GuzzleClient(['exceptions' => false, 'handler' => $guzzleHandlerStack]);
        $this->apiClient = $client ?? Client::getInstance();
    }

    /**
     * Perform a GET request and return the parsed body as response.
     *
     * @param string $urlPath The URL path to GET.
     *
     * @throws \Exonet\Api\Exceptions\ExonetApiException If there was a problem with the request.
     *
     * @return ApiResource|ApiResourceSet The requested URL path transformed to a single or multiple resources.
     */
    public function get(string $urlPath)
    {
        $this->apiClient->log()->debug('Sending [GET] request', ['url' => $this->apiClient->getApiUrl().$urlPath]);

        $request = new Request('GET', $this->apiClient->getApiUrl().$urlPath, $this->getDefaultHeaders());

        $response = $this->httpClient->send($request);

        return $this->parseResponse($response);
    }

    /**
     * Convert the data to JSON and post it to a URL.
     *
     * @param string $urlPath The URL to post to.
     * @param array  $data    An array with data to post to the API.
     *
     * @throws \Exonet\Api\Exceptions\ExonetApiException If there was a problem with the request.
     *
     * @return ApiResource|ApiResourceIdentifier|ApiResourceSet The response from the API, converted to resources.
     */
    public function post(string $urlPath, array $data)
    {
        $this->apiClient->log()->debug('Sending [POST] request', ['url' => $this->apiClient->getApiUrl().$urlPath]);

        $request = new Request(
            'POST',
            $this->apiClient->getApiUrl().$urlPath,
            $this->getDefaultHeaders(),
            json_encode($data)
        );

        $response = $this->httpClient->send($request);

        return $this->parseResponse($response);
    }

    /**
     * Make a DELETE call to the API.
     *
     * @param string $urlPath The url to make the DELETE request to.
     */
    public function delete(string $urlPath)
    {
        $this->apiClient->log()->debug('Sending [POST] request', ['url' => $this->apiClient->getApiUrl().$urlPath]);

        $request = new Request(
            'DELETE',
            $this->apiClient->getApiUrl().$urlPath,
            $this->getDefaultHeaders()
        );

        $this->httpClient->send($request);
    }

    /**
     * Parse the call response to an ApiResource or ApiResourceSet object or throw the correct error if something went
     * wrong.
     *
     * @param PsrResponse $response The call response.
     *
     * @throws \Exonet\Api\Exceptions\ExonetApiException If there was a problem with the request.
     *
     * @return ApiResourceIdentifier|ApiResource|ApiResourceSet The structured response.
     */
    private function parseResponse(PsrResponse $response)
    {
        $this->apiClient->log()->debug('Request completed', ['statusCode' => $response->getStatusCode()]);

        if ($response->getStatusCode() < 300) {
            $contents = $response->getBody()->getContents();

            $decodedContent = json_decode($contents);

            // Create collection of resources when returned data is an array.
            if (is_array($decodedContent->data)) {
                return new ApiResourceSet($contents);
            }

            // Convert single item into resource or resource identifier.
            if (isset($decodedContent->data->attributes)) {
                return new ApiResource($decodedContent->data->type, $contents);
            }

            return new ApiResourceIdentifier($decodedContent->data->type, $decodedContent->data->id);
        }

        (new ResponseExceptionHandler($response))->handle();
    }

    /**
     * Get the headers that are default for each request.
     *
     * @return string[] The headers.
     */
    private function getDefaultHeaders() : array
    {
        return [
            'Authorization' => sprintf('Bearer %s', $this->apiClient->getAuth()->getToken()),
            'Accept' => 'application/vnd.Exonet.v1+json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'exonet-api-php/'.Client::CLIENT_VERSION,
        ];
    }
}
