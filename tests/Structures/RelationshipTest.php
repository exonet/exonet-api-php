<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Request;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{
    public function testConstruct()
    {
        $relationshipClass = new Relationship(
            'something_related',
            'test_resources',
            'ABC1'
        );

        $this->assertAttributeEquals('something_related', 'name', $relationshipClass);
        $this->assertAttributeInstanceOf(Request::class, 'request', $relationshipClass);

        // Test parsing url pattern to url.
        $this->assertAttributeEquals(
            '/test_resources/ABC1/relationships/something_related',
            'url',
            $relationshipClass
        );
    }
}
