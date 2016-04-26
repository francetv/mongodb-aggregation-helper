# MongoDB Aggregation helper

Class to help to build mongodb aggregation query. 

## Install

In composer.json

```
"repositories": [
    {
        "type": "vcs",
        "url": "git@gitlab.ftven.net:team-infini/mongodb-aggregation-helper.git"
    }
]
```

And run composer

```
composer require ftv/mongodb-aggregation-helper
```

## How to use

Create a new instance of ```Ftv\MongoDB\Aggregation``` with an instance of \MongoCollection as parameter.
 

You can use method to build aggregation. There are chainable.

```
$aggregation = $this->createAggregate()
    ->addMatch([
        'groupId' => 'group-id',
        'publication.startDate' => ['$gte' => new \MongoDate() ],
    ])
    ->addSort([['field' => 'updated', "order" => 'desc']])
    ->addProject([
        'slug' => 1,
        'diff' => ['$subtract' => [new \MongoDate(), '$publication.startDate']]
    ]);
```
   
            
Then, execute it.

```
$aggregation->execute();
```
            