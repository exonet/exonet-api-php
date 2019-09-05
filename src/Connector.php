<?php

declare(strict_types=1);

namespace Exonet\Api;

use Exonet\Api\Exceptions\ExonetApiException;
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
     * @var GuzzleClient The HTTP client instance.
     */
    private static $httpClient;

    /**
     * @var HandlerStack|null The Guzzle handler stack to use, if not default.
     */
    private static $guzzleHandlerStack;

    /**
     * @var Client The API client.
     */
    private $apiClientInstance;

    /**
     * Connector constructor.
     *
     * @param HandlerStack|null $guzzleHandlerStack Optional Guzzle handlers.
     * @param Client|null       $client             The client instance.
     */
    public function __construct(?HandlerStack $guzzleHandlerStack = null, ?Client $client = null)
    {
        self::$guzzleHandlerStack = $guzzleHandlerStack;
        $this->apiClientInstance = $client;
    }

    /**
     * Perform a GET request and return the parsed body as response.
     *
     * @param string $urlPath The URL path to GET.
     *
     * @return ApiResource|ApiResourceSet The requested URL path transformed to a single or multiple resources.
     */
    public function get(string $urlPath)
    {
        $apiUrl = $this->apiClient()->getApiUrl().$urlPath;
        $this->apiClient()->log()->debug('Sending [GET] request', ['url' => $apiUrl]);

        $request = new Request('GET', $apiUrl, $this->getDefaultHeaders());

        $response = self::httpClient()->send($request);

        return $this->parseResponse($response);
    }

    /**
     * Convert the data to JSON and post it to a URL.
     *
     * @param string $urlPath The URL to post to.
     * @param array  $data    An array with data to post to the API.
     *
     * @return ApiResource|ApiResourceIdentifier|ApiResourceSet The response from the API, converted to resources.
     */
    public function post(string $urlPath, array $data)
    {
        $apiUrl = $this->apiClient()->getApiUrl().$urlPath;
        $this->apiClient()->log()->debug('Sending [POST] request', ['url' => $apiUrl]);

        $request = new Request(
            'POST',
            $apiUrl,
            $this->getDefaultHeaders(),
            json_encode($data)
        );

        $response = self::httpClient()->send($request);

        return $this->parseResponse($response);
    }

    /**
     * Convert the data to JSON and patch it to a URL.
     *
     * @param string $urlPath The URL to patch to.
     * @param array  $data    An array with data to patch to the API.
     *
     * @return bool True when the patch succeeded.
     */
    public function patch(string $urlPath, array $data) : bool
    {
        $apiUrl = $this->apiClient()->getApiUrl().$urlPath;
        $this->apiClient()->log()->debug('Sending [PATCH] request', ['url' => $apiUrl]);

        $request = new Request(
            'PATCH',
            $apiUrl,
            $this->getDefaultHeaders(),
            json_encode($data)
        );

        self::httpClient()->send($request);

        return true;
    }

    /**
     * Make a DELETE call to the API.
     *
     * @param string $urlPath The url to make the DELETE request to.
     * @param array  $data    (Optional) The data to send along with the DELETE request.
     */
    public function delete(string $urlPath, array $data = []) : void
    {
        $apiUrl = $this->apiClient()->getApiUrl().$urlPath;
        $this->apiClient()->log()->debug('Sending [DELETE] request', ['url' => $apiUrl]);

        $request = new Request(
            'DELETE',
            $apiUrl,
            $this->getDefaultHeaders(),
            json_encode($data)
        );

        self::httpClient()->send($request);
    }

    /**
     * Parse the call response to an ApiResource or ApiResourceSet object or throw the correct error if something went
     * wrong.
     *
     * @param PsrResponse $response The call response.
     *
     * @throws ExonetApiException If there was a problem with the request.
     *
     * @return ApiResourceIdentifier|ApiResource|ApiResourceSet The structured response.
     */
    private function parseResponse(PsrResponse $response)
    {
        $this->apiClient()->log()->debug('Request completed', ['statusCode' => $response->getStatusCode()]);

        if ($response->getStatusCode() >= 300) {
            (new ResponseExceptionHandler($response))->handle();
        }

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

    /**
     * Get or create an HTTP client based on the configured handler stack. Implement the singleton pattern so the HTTP
     * client is shared.
     *
     * @return GuzzleClient The HTTP client instance.
     */
    private static function httpClient() : GuzzleClient
    {
        $stackHash = spl_object_hash(self::$guzzleHandlerStack ?? new \stdClass());
        if (!isset(self::$httpClient[$stackHash])) {
            // Don't let Guzzle throw exceptions, as it is handled by this class.
            self::$httpClient[$stackHash] = new GuzzleClient(['exceptions' => false, 'handler' => self::$guzzleHandlerStack]);
        }

        return self::$httpClient[$stackHash];
    }

    /**
     * Get the API client.
     *
     * @return Client The API client.
     */
    private function apiClient() : Client
    {
        return $this->apiClientInstance ?? Client::getInstance();
    }

    /**
     * Get the headers that are default for each request.
     *
     * @return string[] The headers.
     */
    private function getDefaultHeaders() : array
    {
        return [
            'Authorization' => sprintf('Bearer %s', $this->apiClient()->getAuth()->getToken()),
            'Accept' => 'application/vnd.Exonet.v1+json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'exonet-api-php/'.Client::CLIENT_VERSION,
        ];
    }
}
