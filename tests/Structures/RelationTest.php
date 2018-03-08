<?php

namespace Exonet\Api\Structures;

use Exonet\Api\Request;
use PHPUnit\Framework\TestCase;

class RelationTest extends TestCase
{
    private $singleRelation = [
        'links' => ['self' => 'http://self.test', 'related' => 'http://related.test'],
        'data' => ['id' => 'testId', 'type' => 'testType'],
    ];

    private $multiRelation = [
        'links' => ['self' => 'http://self.test', 'related' => 'http://related.test'],
        'data' => [
            ['id' => 'testId1', 'type' => 'testType1'],
            ['id' => 'testId2', 'type' => 'testType2'],
        ],
    ];

    public function testCountForSingleRelation()
    {
        $relationClass = new Relation('testRelation', $this->singleRelation);

        $this->assertSame(1, $relationClass->count());
    }

    public function testCountForMultiRelation()
    {
        $relationClass = new Relation('testRelation', $this->multiRelation);

        $this->assertSame(2, $relationClass->count());
    }

    public function testGetIteratorForSingleRelation()
    {
        $relationClass = new Relation('testRelation', $this->singleRelation);

        $timesLooped = 0;
        foreach ($relationClass as $index => $relationItem) {
            $timesLooped++;
            $this->assertSame('testId', $relationItem['id']);
            $this->assertSame('testType', $relationItem['type']);
        }

        $this->assertSame(1, $timesLooped);
    }

    public function testGetIteratorForMultiRelation()
    {
        $relationClass = new Relation('testRelation', $this->multiRelation);

        $timesLooped = 0;
        foreach ($relationClass as $index => $relationItem) {
            $timesLooped++;
            $this->assertSame('testId'.($index + 1), $relationItem['id']);
            $this->assertSame('testType'.($index + 1), $relationItem['type']);
        }

        $this->assertSame(2, $timesLooped);
    }

    public function testCall()
    {
        $relationClass = new Relation('testRelation', $this->singleRelation);

        $result = $relationClass->filter('test');

        $this->assertInstanceOf(Request::class, $result);
    }

    public function testRaw()
    {
        $relationClassSingle = new Relation('testRelation', $this->singleRelation);
        $relationClassMulti = new Relation('testRelation', $this->multiRelation);

        $this->assertEquals(['id' => 'testId', 'type' => 'testType'], $relationClassSingle->raw());

        $this->assertEquals([
            ['id' => 'testId1', 'type' => 'testType1'],
            ['id' => 'testId2', 'type' => 'testType2'],
        ], $relationClassMulti->raw());
    }
}
