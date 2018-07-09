<?php

declare(strict_types=1);

namespace Exonet\Api\Structures;

use ArrayAccess;
use ArrayIterator;
use Exonet\Api\Exceptions\ValidationException;
use IteratorAggregate;

/**
 * An ApiResourceSet is a collection of several ApiResource instances that are retrieved from the API.
 */
class ApiResourceSet implements IteratorAggregate, ArrayAccess
{
    /**
     * @var \Exonet\Api\Structures\ApiResource[] The returned resources.
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
     * ApiResourceSet constructor.
     *
     * @param string $contents The results from the API as encoded JSON string.
     */
    public function __construct(string $contents)
    {
        $results = json_decode($contents, true);

        foreach ($results['data'] as $resourceItem) {
            $this->resources[] = new ApiResource($resourceItem);
        }

        $this->meta = $results['meta'];
        $this->links = $results['links'];
    }

    /**
     * Return the data when this class is called as array/in a loop.
     *
     * @return ArrayIterator The array iterator.
     */
    public function getIterator() : ArrayIterator
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
    public function offsetExists($offset) : bool
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
    public function offsetSet($offset, $value) : void
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
    public function offsetUnset($offset) : void
    {
        unset($this->resources[$offset]);
    }
}
