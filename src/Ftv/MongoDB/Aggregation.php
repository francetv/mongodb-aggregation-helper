<?php

namespace Ftv\MongoDB;

/**
 * Class to help building aggregation query
 */
class Aggregation
{
    /**
     * @var array
     */
    private $pipeline = [];

    /**
     * @var \MongoCollection
     */
    private $collection;

    /**
     * @param \MongoCollection $collection
     */
    public function __construct(\MongoCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param array $match
     *
     * @return $this
     */
    public function addMatch(array $match)
    {
        if (!$match) {
            return $this;
        }

        $this->pipeline[] = [
            '$match' => $match
        ];

        return $this;
    }

    /**
     * @param array $group
     *
     * @return $this
     */
    public function addGroup(array $group)
    {
        if (!$group) {
            return $this;
        }

        $this->pipeline[] = [
            '$group' => $group
        ];

        return $this;
    }

    /**
     * @param array $sorts
     *
     * @return $this
     */
    public function addSort(array $sorts)
    {
        if (!$sorts) {
            return $this;
        }

        $sortsFormatted = [];
        foreach ($sorts as $sort) {
            if (!isset($sort['field'], $sort['order'])) {
                continue;
            }
            $sortsFormatted[$sort['field']] = $sort['order'] == "desc" ? -1 : 1;
        }

        if (!$sortsFormatted) {
            return $this;
        }

        $this->pipeline[] = [
            '$sort' => $sortsFormatted
        ];

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function addLimit($limit = 100)
    {
        $this->pipeline[] = [
            '$limit' => $limit
        ];

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function addOffset($offset = 0)
    {
        $this->pipeline[] = [
            '$skip' => $offset
        ];

        return $this;
    }

    /**
     * @param array $project
     *
     * @return $this
     */
    public function addProject(array $project)
    {
        if (!$project) {
            return $this;
        }

        $this->pipeline[] = [
            '$project' => $project
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getPipeline()
    {
        return $this->pipeline;
    }

    /**
     * Execute aggregation query
     *
     * @return array
     */
    public function execute()
    {
        $result = $this->collection->aggregate($this->pipeline);

        if (!isset($result['result'])) {
            return [];
        }

        return $result['result'];
    }
}
