<?php

declare(strict_types=1);

namespace Exonet\Api\Structures;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exonet\Api\Client;
use Exonet\Api\Connector;
use Exonet\Api\Exceptions\ValidationException;
use IteratorAggregate;

/**
 * An ApiResourceSet is a collection of several ApiResource instances that are retrieved from the API.
 */
class ApiResourceSet implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * @var ApiResource[] The returned resources.
     */
    private $resources;

    /**
     * @var mixed[] The resource meta data.
     */
    private $meta;

    /**
     * @var string[] The links for this resource set.
     */
    private $links;

    /**
     * @var Connector Class to use for making pagination requests.
     */
    private $connector;

    /**
     * ApiResourceSet constructor.
     *
     * @param array|string   $resources The resources from the API as encoded JSON string or a similar array.
     * @param Connector|null $connector The connector used for pagination requests. If null, the default connect will be
     *                                  used.
     */
    public function __construct($resources, Connector $connector = null)
    {
        $this->connector = $connector ?? new Connector();

        if (is_string($resources)) {
            $resources = json_decode($resources, true);
        }

        if (isset($resources['data'])) {
            foreach ($resources['data'] as $resourceItem) {
                if (isset($resourceItem['attributes'])) {
                    $this->resources[] = new ApiResource($resourceItem['type'], $resourceItem);
                } else {
                    $this->resources[] = new ApiResourceIdentifier($resourceItem['type'], $resourceItem['id']);
                }
            }
        }

        if (isset($resources['meta'])) {
            $this->meta = $resources['meta'];
        }
        if (isset($resources['links'])) {
            $this->links = $resources['links'];
        }
    }

    /**
     * Return the data when this class is called as array/in a loop.
     *
     * @return ArrayIterator The array iterator.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->resources ?? []);
    }

    /**
     * Whether an offset exists.
     *
     * @param mixed $offset An offset to check for.
     *
     * @return bool true on success or false on failure.
     */
    public function offsetExists($offset): bool
    {
        return isset($this->resources[$offset]);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->resources[$offset];
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     *
     * @throws ValidationException if the provided $value is not an ApiResource.
     */
    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof ApiResource) {
            throw new ValidationException('Only ApiResources can be set.');
        }

        $this->resources[$offset] = $value;
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset The offset to unset.
     */
    public function offsetUnset($offset): void
    {
        unset($this->resources[$offset]);
    }

    /**
     * Implementation of the Countable interface.
     *
     * @return int The number of resources in this set.
     */
    public function count()
    {
        if (empty($this->resources)) {
            return 0;
        }

        return count($this->resources);
    }

    /**
     * Get the total number of resources.
     *
     * @return int|null The total number of resources.
     */
    public function total(): ?int
    {
        return $this->meta['resources']['total'] ?? null;
    }

    /**
     * Get the meta data.
     *
     * @return mixed[]|null The meta data.
     */
    public function meta(): ?array
    {
        return $this->meta;
    }

    /**
     * Get the next page with resources.
     *
     * @return ApiResource|ApiResourceSet|null The next page with resources.
     */
    public function nextPage()
    {
        return $this->navigateToLink('next');
    }

    /**
     * Get the previous page with resources.
     *
     * @return ApiResource|ApiResourceSet|null The previous page with resources.
     */
    public function previousPage()
    {
        return $this->navigateToLink('prev');
    }

    /**
     * Get the first page with resources.
     *
     * @return ApiResource|ApiResourceSet|null The first page with resources.
     */
    public function firstPage()
    {
        return $this->navigateToLink('first');
    }

    /**
     * Get the last page with resources.
     *
     * @return ApiResource|ApiResourceSet|null The last page with resources.
     */
    public function lastPage()
    {
        return $this->navigateToLink('last');
    }

    /**
     * Get the resource for the given link name.
     *
     * @param string $linkName The name of the element in the 'links' array.
     *
     * @return ApiResource|ApiResourceSet|null The requested page with resources.
     */
    private function navigateToLink(string $linkName)
    {
        $linkValue = $this->links[$linkName];

        if ($linkValue === null) {
            return null;
        }

        $link = substr($linkValue, strlen(Client::getInstance()->getApiUrl()));

        return $this->connector->get($link);
    }
}
