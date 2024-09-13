<?php

declare(strict_types=1);

// show all errors and warnings
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Pst\Database\Query\Builder\QueryBuilder;

require_once __DIR__ . '/../../vendor/autoload.php';

$queryBuilder = QueryBuilder::select("schema.table.column as alias");
$queryBuilder = $queryBuilder->select("`schema2`.`table2`.`column2` as `alias2`");
$queryBuilder = $queryBuilder->from("schema.table");
$queryBuilder = $queryBuilder->where("schema.table.column = 123");
$queryBuilder = $queryBuilder->andWhere("schema.table.column = 456");
$queryBuilder = $queryBuilder->orWhere("schema.table.column = 789");
$queryBuilder = $queryBuilder->select("`schema3`.`table3`.`column3` as `alias3`");
$queryBuilder = $queryBuilder->from("schema2.table2");
$queryBuilder = $queryBuilder->groupBy("schema.table.column", "schema2.table2.column2");
$queryBuilder = $queryBuilder->groupBy("schema3.table3.column3");
$queryBuilder = $queryBuilder->having("schema.table.column = 123");
$queryBuilder = $queryBuilder->andHaving("schema.table.column = 456");
$queryBuilder = $queryBuilder->orHaving("schema.table.column = 789");
$queryBuilder = $queryBuilder->orderBy("schema.table.column", "schema2.table2.column2");
$queryBuilder = $queryBuilder->orderBy("schema3.table3.column3");
$queryBuilder = $queryBuilder->limit(10);
$queryBuilder = $queryBuilder->offset(5);
print_r($queryBuilder->getQuery());
