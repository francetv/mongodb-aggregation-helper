<?php

use Ftven\MongoDB\Aggregation;

class AggregationTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildingAndExecute()
    {
        $collection = $this->getMockBuilder('\MongoCollection')->disableOriginalConstructor()->getMock();
        $collection->expects($this->once())->method('aggregate')->will($this->returnValue(['result' => []]));

        $aggregation = new Aggregation($collection);
        $aggregation->addMatch(['field' => 'value']);
        $aggregation->addGroup(['_id' => '$field']);
        $aggregation->addProject(['_id' => '$field']);
        $aggregation->addSort([['field' => 'field', 'order' => 'desc']]);
        $aggregation->addOffset(10);
        $aggregation->addLimit(100);

        $this->assertEquals([
            ['$match' => ['field' => 'value']],
            ['$group' => ['_id' => '$field']],
            ['$project' => ['_id' => '$field']],
            ['$sort' => ['field' => -1]],
            ['$skip' => 10],
            ['$limit' => 100],
        ], $aggregation->getPipeline());

        $this->assertEquals([], $aggregation->execute());
    }

    public function testChainableMethod()
    {
        $collection = $this->getMockBuilder('\MongoCollection')->disableOriginalConstructor()->getMock();

        $aggregation = new Aggregation($collection);
        $aggregation->addMatch([])->addGroup([])->addProject([])->addSort([]);

        $this->assertEquals([], $aggregation->getPipeline());
    }

    public function testExecuteFail()
    {
        $collection = $this->getMockBuilder('\MongoCollection')->disableOriginalConstructor()->getMock();
        $collection->expects($this->once())->method('aggregate')->will($this->returnValue([]));

        $aggregation = new Aggregation($collection);

        $this->assertEquals([], $aggregation->execute());
    }

    public function testSortMalformed()
    {
        $collection = $this->getMockBuilder('\MongoCollection')->disableOriginalConstructor()->getMock();

        $aggregation = new Aggregation($collection);
        $aggregation->addSort([['malformed' => 'field', 'order' => 'desc']]);

        $this->assertEquals([
        ], $aggregation->getPipeline());
    }
}
