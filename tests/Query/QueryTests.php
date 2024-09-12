<?php

declare(strict_types=1);

// show all errors and warnings
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Pst\Database\Query\Builder\QueryBuilder;
use Pst\Database\Query\Builder\SelectQuery\ISelectSelectClause;

require_once __DIR__ . '/../../vendor/autoload.php';

echo "\n\n";

$query = QueryBuilder::select("accounts.id as accountId", "accounts.name as accountName");
$query = $query->from("accounts as a", "attachments as b");
// $query = $query->innerJoin();
// $query = $query->outerJoin();
// $query = $query->leftJoin();
// $query = $query->rightJoin();
// $query = $query->fullJoin();
// $query = $query->where("age = 2");



print_r($query);
// $query = QueryBuilder
//     ::newSelect("accounts.id as accountId", "accounts.name as accountName")
//     ->from("accounts as a", "attachments as b")
//     //->fullJoin()->innerJoin()->leftJoin()->rightJoin()->outerJoin()->where("id=1")

    


//     ;

//print_r($query);

// $query = QueryBuilder
//     ::select("accounts.id as accountId", "accounts.name as accountName")
//     ->from("accounts as a", "attachments as b")
//     // join
//     ->where("age = 2")
//     ->groupBy("accounts.id", "accounts.name")
//     ->having("age = 4")
//     // //having
//     ->orderBy("id desc", "name asc")
//     ->limit(5)
//     ->offset(10)
// ;

// print_r($query->getQuery());