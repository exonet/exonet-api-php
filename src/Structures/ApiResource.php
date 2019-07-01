<?php

declare(strict_types=1);

namespace Exonet\Api\Structures;

/**
 * An ApiResource represents a single resource that is retrieved from the API and allows easy access to its attributes
 * and relations.
 */
class ApiResource extends ApiResourceIdentifier
{
    /**
     * @var mixed[] The attributes for this resource.
     */
    private $attributes;

    /**
     * ApiResource constructor.
     *
     * @param mixed[]|string $contents The contents of the resource, as (already decoded) array or encoded JSON.
     */
    public function __construct($contents)
    {
        $data = is_array($contents) ? $contents : json_decode($contents, true)['data'];
        parent::__construct($data['type'], $data['id']);

        $this->attributes = $data['attributes'];
        $this->relationships = isset($data['relationships']) ? $this->parseRelations($data['relationships']) : null;
    }

    /**
     * Get a specific attribute or set a new value.
     *
     * @param string $attributeName The name of the attribute to get.
     * @param mixed  $newValue      The new attribute value.
     *
     * @return mixed The value of the attribute.
     */
    public function attribute($attributeName, $newValue = null)
    {
        if ($newValue) {
            $this->attributes[$attributeName] = $newValue;
        }

        return $this->attributes[$attributeName];
    }

    /**
     * Parse relationships found in the resource into related resource identifiers.
     *
     * @param string[] $relationships The relationship data.
     *
     * @return Relation[] The parsed relationships.
     */
    private function parseRelations(array $relationships) : array
    {
        $parsedRelations = [];

        foreach ($relationships as $relationName => $relation) {
            $relationship = new Relationship($relationName, $this->type(), $this->id());

            if (isset($relation['data']['type'])) {
                $relationship->setResourceIdentifiers(
                    new ApiResourceIdentifier($relation['data']['type'], $relation['data']['id'])
                );
            } elseif (!empty($relation['data'])) {
                $relationship->setResourceIdentifiers(
                    new ApiResourceSet($relation)
                );
            }

            $parsedRelations[$relationName] = $relationship;
        }

        return $parsedRelations;
    }
}
