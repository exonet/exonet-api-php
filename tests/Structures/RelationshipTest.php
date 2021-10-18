<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Request;
use Mockery;
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

    public function testToJsonNoIdentifier()
    {
        $relationshipClass = new Relationship(
            'something_related',
            'test_resources',
            'ABC1'
        );

        $resultJson = $relationshipClass->toJson();

        $this->assertNull($resultJson);
    }

    public function testToJsonSingleRelation()
    {
        $relationshipClass = new Relationship(
            'something_related',
            'test_resources',
            'ABC1',
        );

        $relationshipClass->setResourceIdentifiers(new ApiResourceIdentifier('related', 'XYZ'));

        $resultJson = $relationshipClass->toJson();

        $this->assertSame(
            [
                'type' => 'related',
                'id' => 'XYZ',
            ],
            $resultJson
        );
    }

    public function testToJsonMultiRelation()
    {
        $relationshipClass = Mockery::mock(
            Relationship::class.'[getResourceIdentifiers]',
            [
                'something_related',
                'test_resources',
                'ABC1'
            ]
        )
        ->makePartial();

        $relationshipClass->shouldReceive('getResourceIdentifiers')->andReturn([
            new ApiResourceIdentifier('related', 'XYZ'),
            new ApiResourceIdentifier('related', '999'),
        ]);

        $resultJson = $relationshipClass->toJson();

        $this->assertSame(
            [
                [
                    'type' => 'related',
                    'id' => 'XYZ',
                ],
                [
                    'type' => 'related',
                    'id' => '999',
                ],
            ],
            $resultJson
        );
    }
}
