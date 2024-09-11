<?php

declare(strict_types=1);

// show all errors and warnings
error_reporting(E_ALL);

use Pst\Database\Query\Builder\QueryBuilder;
use Pst\Database\Query\Builder\Clauses\Having;
use Pst\Database\Query\Builder\Clauses\Where;

require_once __DIR__ . '/../../vendor/autoload.php';

$query = QueryBuilder::select('id', 'name')
    ->from('users', 'groups')
    ->where('id=1')->or(
        Where::new('name = "John"', "lastname = 'Doe'")
    )
    ->groupBy('id')
    ->having('id=1')->or(
        Having::new('name = "John"')->and("lastname = 'Doe'")
    )
    ->orderBy('name')
    ->limit(10)
    ->offset(5);

echo($query->getQuery());
    