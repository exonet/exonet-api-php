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
}
