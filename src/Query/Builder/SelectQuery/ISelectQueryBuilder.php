<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Core\ICoreObject;

use Pst\Database\Query\IQuery;
use Pst\Database\Query\Builder\IAutoJoiner;

interface ISelectQueryBuilder extends ICoreObject {
    public function select(...$selectExpressions): ISelectQueryBuilder;
    public function from(...$fromXxpressions): ISelectQueryBuilder;
    // public function innerJoin(...$expressions): ISelectQueryBuilder;
    // public function outerJoin(...$expressions): ISelectQueryBuilder;
    // public function leftJoin(...$expressions): ISelectQueryBuilder;
    // public function rightJoin(...$expressions): ISelectQueryBuilder;
    // public function fullJoin(...$expressions): ISelectQueryBuilder;
    public function where(...$whereExpressions): ISelectQueryBuilder;
    public function andWhere(...$andWhereExpressions): ISelectQueryBuilder;
    public function orWhere(...$orWhereExpressions): ISelectQueryBuilder;
    public function groupBy(...$groupByExpressions): ISelectQueryBuilder;
    public function having(...$havingExpressions): ISelectQueryBuilder;
    public function andHaving(...$andHavingExpressions): ISelectQueryBuilder;
    public function orHaving(...$orHavingExpressions): ISelectQueryBuilder;
    public function orderBy(...$orderByExpressions): ISelectQueryBuilder;
    public function limit(int $limit): ISelectQueryBuilder;
    public function offset(int $offset): ISelectQueryBuilder;
    public function getQuery(?IAutoJoiner $autoJoiner = null): IQuery;
}