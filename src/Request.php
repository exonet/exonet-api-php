<?php

declare(strict_types=1);

namespace Exonet\Api;

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
     * @param null|string                $resource  The resource to get.
     * @param \Exonet\Api\Connector|null $connector Optional connector instance to use.
     */
    public function __construct(string $resource, ?Connector $connector = null)
    {
        $this->resource = $resource;
        $this->connector = $connector ?? new Connector();
    }

    /**
     * Get the resource or, if specified, the resource that belongs to the ID.
     *
     * @param null|string $id Optional ID to get a specific resource.
     *
     * @throws \Exonet\Api\Exceptions\ExonetApiException If there was a problem with the request.
     *
     * @return \Exonet\Api\Structures\ApiResource|\Exonet\Api\Structures\ApiResourceSet The requested data transformed
     *                                                                                  to a single or multiple resources.
     */
    public function get(?string $id = null)
    {
        return $this->connector->get($this->prepareUrl($id));
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
     * Prepare the URL for the resource, including (optional) ID and query string parameters. Strip the API url, remove
     * excessive slashes and add query string parameters.
     *
     * @param null|string $id The resource ID to get.
     *
     * @return string The fully prepared URL.
     */
    private function prepareUrl(?string $id = null) : string
    {
        return sprintf(
            '%s/%s?%s',
            trim(str_replace(Connector::API_URL, '', $this->resource), '/'),
            $id,
            http_build_query($this->queryStringParameters)
        );
    }
}
