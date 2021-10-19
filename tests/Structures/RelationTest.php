<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Exceptions\InvalidRequestException;
use Exonet\Api\Request;
use PHPUnit\Framework\TestCase;

class RelationTest extends TestCase
{
    public function testCall()
    {
        $relationClass = new Relation('testRelation', 'test_resources', 'ABC1');

        $result = $relationClass->filter('test');

        $this->assertInstanceOf(Request::class, $result);
    }

    public function testCallRequestNull()
    {
        $this->expectException(InvalidRequestException::class);

        $relationClass = new Relation('testRelation', 'test_resources');

        $relationClass->filter('test');
    }
}
