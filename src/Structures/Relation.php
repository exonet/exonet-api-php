<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Exceptions\InvalidRequestException;
use Exonet\Api\Request;

/**
 * The Relation Class represents a relation of an ApiResource with a predefined request to get the related resource.
 */
class Relation
{
    /**
     * @var string Pattern to create the relation url.
     */
    protected $urlPattern = '/%s/%s/%s';

    /**
     * @var string The url for the relation data.
     */
    private $url;

    /**
     * @var string The name of the relation.
     */
    private $name;

    /**
     * @var Request The prepared request to get the relation data.
     */
    private $request;

    /**
     * @var ApiResourceSet|ApiResourceIdentifier The related resource identifier or a ApiResourceSet.
     */
    private $resourceIdentifiers;

    /**
     * Relation constructor.
     *
     * @param string $relationName The name of the relation.
     * @param string $originType   The resource type of the origin resource.
     * @param string $originId     The resource ID of the origin resource.
     */
    public function __construct(string $relationName, string $originType = null, string $originId = null)
    {
        $this->name = $relationName;

        if ($originType && $originId) {
            $this->url = sprintf(
                $this->urlPattern,
                $originType,
                $originId,
                $relationName
            );

            $this->request = new Request($this->url);
        }

    }

    /**
     * Pass unknown calls to the Request instance.
     *
     * @param string $methodName The method to call.
     * @param array  $arguments  The method arguments.
     *
     * @return Request|ApiResource|ApiResourceSet The request instance or retrieved resource (set).
     */
    public function __call($methodName, $arguments)
    {
        if (is_null($this->request)) {
            throw new InvalidRequestException('No request available, incomplete relation');
        }
        return call_user_func_array([$this->request, $methodName], $arguments);
    }

    /**
     * Get the resource identifiers for this relation.
     *
     * @return ApiResourceSet|ApiResourceIdentifier The resource identifier or a resource set.
     */
    public function getResourceIdentifiers()
    {
        return  $this->resourceIdentifiers;
    }

    /**
     * Replace the related resource identifiers with new data.
     *
     * @param ApiResourceSet|ApiResourceIdentifier $newRelationship A new resource identifier or a new resource set.
     *
     * @return $this
     */
    public function setResourceIdentifiers($newRelationship)
    {
        $this->resourceIdentifiers = $newRelationship;

        return  $this;
    }
}
