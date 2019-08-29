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

    /**
     * @var string[] Array to keep track of relationships that are changed.
     */
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
    public function type() : string
    {
        return $this->resourceType;
    }

    /**
     * Get the resource Id.
     *
     * @return string|null The resource Id.
     */
    public function id() : ?string
    {
        return $this->id;
    }

    /**
     * Make a GET request to the resource.
     *
     * @return Resource|ResourceSet A resource or resource set.
     */
    public function get()
    {
        return $this->request->get($this->id);
    }

    /**
     * Delete this resource from the API.
     */
    public function delete() : void
    {
        // If there are no changed relationships, perform a 'normal' delete.
        if (empty($this->changedRelationships)) {
            $this->request->delete($this->id());

            return;
        }

        // If there are changed relationships, transform them to JSON and send a DELETE to the relationship endpoint.
        foreach ($this->changedRelationships as $relationship) {
            $relationData = $this->relationship($relationship);
            if (is_array($relationData)) {
                array_walk($relationData, function (&$relationItem) {
                    $relationItem = $relationItem->toJson();
                });
            } else {
                $relationData = $relationData->toJson();
            }

            $this->request->delete($this->id().'/relationships/'.$relationship, ['data' => $relationData]);
        }
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
     * @param string                                  $name     The name of the relationship.
     * @param ResourceIdentifier|ResourceIdentifier[] $resource
     *
     * @return Relation|Relationship|$this The requested relation data or the current resource when setting a relation.
     */
    public function relationship(string $name, $resource = null)
    {
        // If there are is only a single argument, get the relation.
        if (func_num_args() === 1) {
            // Check if the relationship is already defined. If not, create it now.
            if (!isset($this->relationships[$name])) {
                $this->relationships[$name] = new Relationship($name, $this->type(), $this->id());
            }

            return $this->relationships[$name];
        }

        // Set the relation data.
        if (is_array($resource)) {
            foreach ($resource as $resourceIdentifier) {
                $this->relationships[$name][] = $resourceIdentifier;
            }
        } else {
            $this->relationships[$name] = $resource;
        }

        $this->changedRelationships[] = $name;

        return $this;
    }

    /**
     * Transform the set identifiers for this resource to an array that can be used for JSON.
     */
    protected function toJson() : array
    {
        return [
            'type' => $this->type(),
            'id' => $this->id(),
        ];
    }
}
