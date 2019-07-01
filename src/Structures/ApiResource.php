<?php

declare(strict_types=1);

namespace Exonet\Api\Structures;

use Exonet\Api\Exceptions\ExonetApiException;

/**
 * An ApiResource represents a single resource that is retrieved from the API and allows easy access to its attributes
 * and relations.
 */
class ApiResource extends ApiResourceIdentifier
{
    /**
     * @var mixed[] The attributes for this resource.
     */
    private $attributes = [];

    /**
     * ApiResource constructor.
     *
     * @param string         $type     The resource type.
     * @param mixed[]|string $contents The contents of the resource, as (already decoded) array or encoded JSON.
     */
    public function __construct($type, $contents=[])
    {
        $data = is_array($contents) ? $contents : json_decode($contents, true)['data'];
        parent::__construct(
            $type,
            $data['id'] ?? null
        );

        if (array_key_exists('attributes', $data)) {
            $this->attributes = $data['attributes'];
        }

        if (array_key_exists('relationships', $data)) {
            $this->relationships = $this->parseRelations($data['relationships']);
        }
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

        if (!array_key_exists($attributeName, $this->attributes)) {
            throw new ExonetApiException('Undefined attribute');
        }

        return $this->attributes[$attributeName];
    }

    /**
     * Post this resource to the API.
     *
     * @return ApiResource The newly created resource.
     */
    public function post()
    {
        return $this->request->post($this->toJson());
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

    /**
     * Get the json representation of the resource.
     *
     * @return array|null Array that can be used as json.
     */
    private function toJson() : array
    {
        $json = [
            'data' => [
                'type' => $this->type(),
                'attributes' => []
            ]
        ];

        if ($this->id()) {
            $json['data']['id'] = $this->id();
        }

        // Set the attributes in the json.
        array_walk($this->attributes, function($attributeValue, $attributeName) use (&$json) {
            $json['data']['attributes'][$attributeName] = $attributeValue;
        });

        // Set relations.
        if ($this->relationships) {
            array_walk($this->relationships, function(Relationship $relation, $name) use (&$json) {
                $json['data']['relationships'][$name]['data'] = $relation->toJson();
            });
        }

        return $json;
    }
}
