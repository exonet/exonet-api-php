<?php

declare(strict_types=1);

namespace Exonet\Api\Structures;

use ArrayIterator;
use IteratorAggregate;

/**
 * An ApiResourceSet is a collection of several ApiResource instances that are retrieved from the API.
 */
class ApiResourceSet implements IteratorAggregate
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
}
