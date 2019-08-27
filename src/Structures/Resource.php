<?php

declare(strict_types=1);

namespace Exonet\Api\Structures;

use Exonet\Api\Exceptions\ExonetApiException;

/**
 * An Resource represents a single resource that is retrieved from the API and allows easy access to its attributes
 * and relations.
 */
class Resource extends ResourceIdentifier
{
    /**
     * @var mixed[] The attributes for this resource.
     */
    private $attributes = [];

    private $changedAttributes = [];

    /**
     * Resource constructor.
     *
     * @param string         $type     The resource type.
     * @param mixed[]|string $contents The contents of the resource, as (already decoded) array or encoded JSON.
     */
    public function __construct(string $type, $contents = [])
    {
        $data = is_array($contents) ? $contents : json_decode($contents, true)['data'];

        // If decoding of JSON has failed, assume the $contents is a hashid.
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $data['id'] = $contents;
        }

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
     * @return mixed The value of the attribute or the ApiResource class when setting an attribute.
     */
    public function attribute($attributeName, $newValue = null)
    {
        // If there are two arguments given, set the value.
        if (func_num_args() === 2) {
            $this->attributes[$attributeName] = $newValue;
            $this->changedAttributes[] = $attributeName;

            return $this;
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
     * Patch this resource to the API.
     *
     * @return bool True when the patch has succeeded.
     */
    public function patch() : bool
    {
        if (!empty($this->changedAttributes)) {
            $this->request->patch($this->id(), $this->toJson(true));
        }

        if (!empty($this->changedRelationships)) {
            $relations = $this->toJson(false, true);
            foreach($relations['data']['relationships'] as $relationName => $relationData) {
                $this->request->patch($this->id().'/relationships/'.$relationName, $relationData);
            }
        }

        return true;
    }

    public function resetChangedAttributes()
    {
        $this->changedAttributes = [];

        return $this;
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
                    new ResourceIdentifier($relation['data']['type'], $relation['data']['id'])
                );
            } elseif (!empty($relation['data'])) {
                $relationship->setResourceIdentifiers(
                    new ResourceSet($relation)
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
    private function toJson(bool $onlyChangedAttributes = false, $onlyChangedRelations = false) : array
    {
        $json = [
            'data' => [
                'type' => $this->type(),
                'attributes' => [],
            ],
        ];

        if ($this->id()) {
            $json['data']['id'] = $this->id();
        }

        // Set the attributes in the json.
        if ($this->attributes && $onlyChangedRelations === false) {
            array_walk(
                $this->attributes,
                function ($attributeValue, $attributeName) use ($onlyChangedAttributes, &$json) {
                    if ($onlyChangedAttributes === false || in_array($attributeName, $this->changedAttributes, true)) {
                        $json['data']['attributes'][$attributeName] = $attributeValue;
                    }
                }
            );
        }

        // Set relations.
        if ($this->relationships && $onlyChangedAttributes === false) {
            array_walk(
                $this->relationships,
                function (Relationship $relation, $name) use ($onlyChangedRelations, &$json) {
                    if ($onlyChangedRelations === false || in_array($name, $this->changedRelationships, true)) {
                        $json['data']['relationships'][$name]['data'] = $relation->toJson();
                    }
                }
            );
        }

        return $json;
    }
}
