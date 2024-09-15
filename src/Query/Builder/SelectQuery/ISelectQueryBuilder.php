<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Database\Query\IQuery;
use Pst\Database\Query\Builder\IQueryBuilder;

interface ISelectQueryBuilder extends IQueryBuilder {
    public function select(...$selectExpressions): ISelectQueryBuilder;
    public function from(...$fromXxpressions): ISelectQueryBuilder;
    public function join(...$innerJoinExpressions): ISelectQueryBuilder;
    public function innerJoin(...$innerJoinExpressions): ISelectQueryBuilder;
    public function leftJoin(...$leftJoinExpressions): ISelectQueryBuilder;
    public function rightJoin(...$rightJoinExpressions): ISelectQueryBuilder;
    public function where($whereExpression): ISelectQueryBuilder;
    public function andWhere($andWhereExpression): ISelectQueryBuilder;
    public function orWhere($orWhereExpression): ISelectQueryBuilder;
    public function groupBy(...$groupByExpressions): ISelectQueryBuilder;
    public function having(...$havingExpressions): ISelectQueryBuilder;
    public function andHaving(...$andHavingExpressions): ISelectQueryBuilder;
    public function orHaving(...$orHavingExpressions): ISelectQueryBuilder;
    public function orderBy(...$orderByExpressions): ISelectQueryBuilder;
    public function limit(int $limit): ISelectQueryBuilder;
    public function offset(int $offset): ISelectQueryBuilder;
    public function getQuery(): IQuery;
}