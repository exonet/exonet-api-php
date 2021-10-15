<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Request;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{
    public function testCall()
    {
        $relationshipClass = new Relationship(
            'something_related',
            'test_resources',
            'ABC1'
        );

        $result = $relationshipClass->filter('test');

        $this->assertInstanceOf(Request::class, $result);
    }
}
