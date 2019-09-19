<?php

declare(strict_types=1);

namespace Exonet\Api;

use Exonet\Api\Structures\ApiResource;
use Exonet\Api\Structures\ApiResourceIdentifier;
use Exonet\Api\Structures\ApiResourceSet;

/**
 * This class is responsible for building a valid API request that can be passed to the Connector.
 */
class Request
{
    /**
     * @var string The resource name.
     */
    private $resource;

    /**
     * @var Connector The connector instance.
     */
    private $connector;

    /**
     * @var mixed[] Optional query string parameters.
     */
    private $queryStringParameters = [
        'page' => [
            'size' => null,
            'number' => null,
        ],
        'filter' => [],
    ];

    /**
     * Request constructor.
     *
     * @param null|string    $resource  The resource to get.
     * @param Connector|null $connector Optional connector instance to use.
     */
    public function __construct(string $resource, ?Connector $connector = null)
    {
        $this->resource = $resource;
        $this->connector = $connector ?? new Connector();
    }

    /**
     * Create a resource identifier for a request.
     *
     * @param string $id The identifier for the resource.
     *
     * @return ApiResourceIdentifier The resource identifier.
     */
    public function id(string $id) : ApiResourceIdentifier
    {
        return new ApiResourceIdentifier($this->resource, $id);
    }

    /**
     * Get the resource or, if specified, the resource that belongs to the ID.
     *
     * @param null|string $id Optional ID to get a specific resource.
     *
     * @return ApiResource|ApiResourceSet The requested data transformed to a single or multiple resources.
     */
    public function get(?string $id = null)
    {
        return $this->connector->get($this->prepareUrl($id));
    }

    /**
     * Post new data to the API.
     *
     * @param array       $payload   The payload to post to the API.
     * @param string|null $appendUrl (Optional) String to append to the URL.
     *
     * @return ApiResource|ApiResourceIdentifier|ApiResourceSet The parsed response transformed to resources.
     */
    public function post(array $payload, string $appendUrl = null)
    {
        return $this->connector->post(
            trim($this->resource, '/').'/'.$appendUrl,
            $payload
        );
    }

    /**
     * Patch data to the API. Will return 'true' when successful. If not successful an exception
     * is thrown.
     *
     * @param string $id      The ID of the resource to patch.
     * @param array  $payload The payload to post to the API.
     *
     * @return true When the patch succeeded.
     */
    public function patch(string $id, array $payload) : bool
    {
        return $this->connector->patch(
            trim($this->resource, '/').'/'.$id,
            $payload
        );
    }

    /**
     * Delete a resource. Will return 'true' when successful. If not successful an exception is thrown.
     *
     * @param string $id   The ID of the resource to delete.
     * @param array  $data (Optional) Data to send along with the request.
     *
     * @return true When the delete was successful.
     */
    public function delete(string $id, array $data = []) : bool
    {
        return $this->connector->delete(
            trim($this->resource, '/').'/'.$id,
            $data
        );
    }

    /**
     * Set the page size for the request. This is the maximum number of resources that is returned in a single call.
     *
     * @param int $pageSize The page size.
     *
     * @return self This current Request instance.
     */
    public function size(int $pageSize) : self
    {
        $this->queryStringParameters['page']['size'] = $pageSize;

        return $this;
    }

    /**
     * Set the page number for the request.
     *
     * @param int $pageNumber The page number.
     *
     * @return self This current Request instance.
     */
    public function page(int $pageNumber) : self
    {
        $this->queryStringParameters['page']['number'] = $pageNumber;

        return $this;
    }

    /**
     * Set a filter for the request.
     *
     * @param string $name  The filter name.
     * @param mixed  $value The filter value. Default: true.
     *
     * @return self This current Request instance.
     */
    public function filter(string $name, $value = true) : self
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $this->queryStringParameters['filter'][$name] = $value;

        return $this;
    }

    /**
     * Prepare the URL for the resource, including (optional) ID and query string parameters. Remove
     * excessive slashes and add query string parameters.
     *
     * @param null|string $id The resource ID to get.
     *
     * @return string The fully prepared URL.
     */
    private function prepareUrl(?string $id = null) : string
    {
        $url = trim($this->resource, '/');
        if ($id) {
            $url .= '/'.$id;
        }

        $params = http_build_query($this->queryStringParameters);
        if (!empty($params)) {
            $url .= '?'.$params;
        }

        return $url;
    }
}
