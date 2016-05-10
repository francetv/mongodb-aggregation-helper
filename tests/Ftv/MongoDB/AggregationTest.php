<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 France Télévisions
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and
 * to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of
 * the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

use Ftv\MongoDB\Aggregation;

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
