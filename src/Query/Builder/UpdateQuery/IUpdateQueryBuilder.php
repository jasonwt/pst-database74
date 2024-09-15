<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\UpdateQuery;

use Pst\Database\Query\IQuery;

interface IUpdateQueryBuilder extends IUpdateSetQueryBuilder {
    public function on(...$onExpressions): IUpdateQueryBuilder;

    public function join(...$innerJoinExpressions): IUpdateQueryBuilder;
    public function innerJoin(...$innerJoinExpressions): IUpdateQueryBuilder;
    public function leftJoin(...$leftJoinExpressions): IUpdateQueryBuilder;
    public function rightJoin(...$rightJoinExpressions): IUpdateQueryBuilder;
    public function where($whereExpression): IUpdateQueryBuilder;
    public function andWhere($andWhereExpression): IUpdateQueryBuilder;
    public function orWhere($orWhereExpression): IUpdateQueryBuilder;
    public function limit(int $limit): IUpdateQueryBuilder;
    
    public function getQuery(): IQuery;
}