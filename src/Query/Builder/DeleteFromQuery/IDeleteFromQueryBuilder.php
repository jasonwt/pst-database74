<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\DeleteFromQuery;

use Pst\Database\Query\IQuery;
use Pst\Database\Query\Builder\IQueryBuilder;


interface IDeleteFromQueryBuilder extends IQueryBuilder {
    //public function on(...$onExpressions): IDeleteFromQueryBuilder;

    public function join(...$innerJoinExpressions): IDeleteFromQueryBuilder;
    public function innerJoin(...$innerJoinExpressions): IDeleteFromQueryBuilder;
    public function leftJoin(...$leftJoinExpressions): IDeleteFromQueryBuilder;
    public function rightJoin(...$rightJoinExpressions): IDeleteFromQueryBuilder;
    public function where($whereExpression): IDeleteFromQueryBuilder;
    public function andWhere($andWhereExpression): IDeleteFromQueryBuilder;
    public function orWhere($orWhereExpression): IDeleteFromQueryBuilder;
    public function limit(int $limit): IDeleteFromQueryBuilder;
    
    public function getQuery(): IQuery;
}