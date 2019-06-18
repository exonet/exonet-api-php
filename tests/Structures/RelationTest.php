<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Request;
use PHPUnit\Framework\TestCase;

class RelationTest extends TestCase
{
    public function testConstruct()
    {
        $relationClass = new Relation(
            'something_related',
            'test_resources',
            'ABC1'
        );

        $this->assertAttributeEquals('something_related', 'name', $relationClass);
        $this->assertAttributeInstanceOf(Request::class, 'request', $relationClass);

        // Test parsing url pattern to url.
        $this->assertAttributeEquals(
            '/test_resources/ABC1/something_related',
            'url',
            $relationClass
        );
    }


    public function testCall()
    {
        $relationClass = new Relation('testRelation', 'test_resources', 'ABC1');

        $result = $relationClass->filter('test');

        $this->assertInstanceOf(Request::class, $result);
    }
}
