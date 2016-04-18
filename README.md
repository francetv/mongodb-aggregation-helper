# MongoDB Aggregation helper

Class to help to build mongodb aggregation. 

## Install

```
composer require ftven/mongodb-aggregation-helper
```

## How to use

Create a new instance of ```Ftven\MongoDB\Aggregation``` with an instance of \MongoCollection as parameter.
 

You can use method to build aggregation. There are chainable.

```
$aggregation = $this->createAggregate()
    ->addMatch([
        'groupId' => $groupId,
        'publication.startDate' => ['$gte' => $currentDate ],
    ])
    ->addSort([['field' => 'updated', "order" => 'desc']])
    ->addProject([
        'slug' => 1,
        'diff' => ['$subtract' => [$currentDate, '$publication.startDate']]
    ]);
```
   
            
Then, execute it.

```
$aggregation->execute();
```
            