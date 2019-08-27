<?php

declare(strict_types=1);

namespace Exonet\Api\Structures;

use Exonet\Api\Request;

/**
 * An ApiResourceID is a way to identify a single resource.
 */
class ResourceIdentifier
{
    /**
     * @var string The resource type.
     */
    private $resourceType;

    /**
     * @var string The resource ID.
     */
    private $id;

    /**
     * @var Request A request instance to make calls to the API.
     */
    protected $request;

    /**
     * @var Relation[]|null The relationships for this resource.
     */
    protected $relationships;

    protected $changedRelationships = [];

    /**
     * ResourceIdentifier constructor.
     *
     * @param string  $resourceType The resource type.
     * @param string  $id           The resource ID.
     * @param Request $request      The optional request instance to use.
     */
    public function __construct(string $resourceType, ?string $id = null, ?Request $request = null)
    {
        $this->resourceType = $resourceType;
        $this->id = $id;

        $this->request = $request ?? new Request($resourceType);
    }

    /**
     * Get the resource type.
     *
     * @return string The resource type.
     */
    public function type()
    {
        return $this->resourceType;
    }

    /**
     * Get the resource Id.
     *
     * @return string|null The resource Id.
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Make a GET request to the resource.
     *
     * @return Resource|ResourceSet A resource or resource set.
     * @throws ExonetApiException The exception.
     *
     */
    public function get()
    {
        return $this->request->get($this->id);
    }

    /**
     * Delete this resource from the API.
     */
    public function delete()
    {
        $this->request->delete($this->id());
    }

    /**
     * Get a relation definition to another resource.
     *
     * @param string $name The name of the relation.
     *
     * @return Relation|Relationship
     */
    public function related($name)
    {
        return new Relation($name, $this->type(), $this->id());
    }

    /**
     * Get a relationship definition to another resource.
     *
     * @param string                     $name The name of the relationship.
     * @param ApiResourceIdentifier|null $resource
     *
     * @return Relation|Relationship|$this
     */
    public function relationship(string $name, $resource = null)
    {
        // Check if the relationship is already defined. If not, create it now.
        if (!isset($this->relationships[$name])) {
            $this->relationships[$name] = new Relationship($name, $this->type(), $this->id());
        }

        // If there are two arguments given, set the value.
        if (func_num_args() === 2) {
            $this->relationships[$name]->setResourceIdentifiers($resource);
            $this->changedRelationships[] = $name;

            return $this;
        }

        return $this->relationships[$name];
    }

    public function resetChangedRelationhips()
    {
        $this->changedRelationships = [];

        return $this;
    }
}
