<?php

namespace Exonet\Api\Structures;

/**
 * The Relationship Class represents a relationship of an ApiResource with a predefined request to get the related
 * resource identifiers.
 */
class Relationship extends Relation
{
    /**
     * @var string Pattern to create the relationship url.
     */
    protected $urlPattern = '/%s/%s/relationships/%s';

    /**
     * Get the json representation of the relationship.
     *
     * @return array|null Array that can be used as json, or null when no relationship data.
     */
    public function toJson()
    {
        $resourceIdentifiers = $this->getResourceIdentifiers();
        if (is_null($resourceIdentifiers)) {
            return null;
        }

        if (is_array($resourceIdentifiers)) {
            $json = [];

            foreach ($resourceIdentifiers as $identifier) {
                $json[] = [
                    'type' => $identifier->type(),
                    'id' => $identifier->id(),
                ];
            }
        } else {
            $json = [
                'type' => $resourceIdentifiers->type(),
                'id' => $resourceIdentifiers->id(),
            ];
        }

        return $json;
    }
}
