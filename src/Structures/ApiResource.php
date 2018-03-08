<?php

declare(strict_types=1);

namespace Exonet\Api\Structures;

use ArrayAccess;

/**
 * An ApiResource represents a single resource that is retrieved from the API and allows easy access to its attributes
 * and relations.
 */
class ApiResource implements ArrayAccess
{
    /**
     * @var string The resource type.
     */
    public $type;

    /**
     * @var string The resource ID.
     */
    public $id;

    /**
     * @var mixed[] The attributes for this resource.
     */
    private $attributes;

    /**
     * @var Relation[]|null The relationships for this resource.
     */
    private $relationships;

    /**
     * ApiResource constructor.
     *
     * @param mixed[]|string $contents The contents of the resource, as (already decoded) array or encoded JSON.
     */
    public function __construct($contents)
    {
        $data = is_array($contents) ? $contents : json_decode($contents, true)['data'];

        $this->type = $data['type'];
        $this->id = $data['id'];
        $this->attributes = $data['attributes'];
        $this->relationships = isset($data['relationships']) ? $this->parseRelations($data['relationships']) : null;
    }

    /**
     * Get a specific attribute.
     *
     * @param string $name The name of the attribute to get.
     *
     * @return mixed The value of the attribute.
     */
    public function __get(string $name)
    {
        return $this->attributes[$name];
    }

    /**
     * Set the value of an attribute.
     *
     * @param string $name  The name of the attribute.
     * @param mixed  $value The new attribute value.
     */
    public function __set(string $name, $value) : void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Check if the given $name is set.
     *
     * @param string $name The name.
     *
     * @return bool True when the attribute exists and is set.
     */
    public function __isset(string $name) : bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Get a relation. Arguments will be ignored.
     *
     * @param string $name      The name of the relation.
     * @param mixed  $arguments Arguments passed, will be ignored.
     *
     * @return Relation The relation.
     */
    public function __call(string $name, $arguments) : Relation
    {
        return $this->relationships[$name];
    }

    /**
     * Check if the given offset exists as property (for ID or type) or as attribute. Required by the ArrayAccess
     * interface.
     *
     * @param string $offset The offset.
     *
     * @return bool True when the offset exists.
     */
    public function offsetExists($offset) : bool
    {
        return property_exists($this, $offset) || isset($this->attributes[$offset]);
    }

    /**
     * Get the given offset. Required by the ArrayAccess interface.
     *
     * @param string $offset The offset.
     *
     * @return mixed The offset value.
     */
    public function offsetGet($offset)
    {
        return $this->{$offset} ?? $this->attributes[$offset];
    }

    /**
     * Set the given offset. Required by the ArrayAccess interface.
     *
     * @param string $offset The offset to set.
     * @param mixed  $value  The offset value.
     */
    public function offsetSet($offset, $value) : void
    {
        if ($offset === 'id' || $offset === 'type') {
            $this->{$offset} = $value;

            return;
        }

        $this->attributes[$offset] = $value;
    }

    /**
     * Unset the given offset. Required by the ArrayAccess interface.
     *
     * @param mixed $offset The offset to unset.
     */
    public function offsetUnset($offset) : void
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Parse the relations to a Relation class.
     *
     * @param string[] $relations The relations.
     *
     * @return Relation[] The parsed relations.
     */
    private function parseRelations(array $relations) : array
    {
        $parsedRelations = [];

        foreach ($relations as $relationName => $relationData) {
            $parsedRelations[$relationName] = new Relation($relationName, $relationData);
        }

        return $parsedRelations;
    }
}
