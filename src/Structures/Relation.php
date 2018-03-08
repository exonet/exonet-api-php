<?php

namespace Exonet\Api\Structures;

use ArrayIterator;
use Exonet\Api\Request;
use IteratorAggregate;

/**
 * The Relation Class represents a relation of an ApiResource with a predefined request to get the related resource.
 */
class Relation implements IteratorAggregate
{
    /**
     * @var string The name of the relation.
     */
    private $name;

    /**
     * @var string[] Array with the links.
     */
    private $links;

    /**
     * @var mixed[] (Multi dimensional) array with the relation data.
     */
    private $data;

    /**
     * @var bool Whether or not this relation consists of multiple resources.
     */
    private $singleResource;

    /**
     * @var Request The prepared request to get the relation data.
     */
    private $request;

    /**
     * Relation constructor.
     *
     * @param string $name The name of the relation.
     * @param array  $data The data array of the relation.
     */
    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->data = $data['data'];
        $this->links = $data['links'];
        $this->singleResource = !isset($data['data'][0]);

        $this->request = new Request($this->links['related']);
    }

    /**
     * Return the number of items in this relation.
     *
     * @return int The number of items in this relation.
     */
    public function count() : int
    {
        return $this->singleResource ? 1 : count($this->data);
    }

    /**
     * Get the relation data as defined in the resource.
     *
     * @return string[]|null The relation data or null if the relation is empty.
     */
    public function raw() : ?array
    {
        return $this->data;
    }

    /**
     * Pass unknown calls to the Request instance.
     *
     * @param string $name      The method name.
     * @param array  $arguments The method arguments.
     *
     * @return Request|\Exonet\Api\Structures\ApiResource|\Exonet\Api\Structures\ApiResourceSet The request instance or
     *                                                                                          retrieved resource (set).
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->request, $name], $arguments);
    }

    /**
     * Return the data when this class is called as array/in a loop.
     *
     * @return ArrayIterator The array iterator.
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->singleResource ? [$this->data] : $this->data);
    }
}
