<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder;

use Pst\Core\CoreObject;

use Pst\Database\Query\Builder\Clauses\Select\Select;
use Pst\Database\Query\Builder\SelectQuery\SelectQueryBuilderTrait;
use Pst\Database\Query\Builder\SelectQuery\ISelectPreFromSelectQueryBuilder;

final class QueryBuilder {
    private function __construct() {}

    public static function select(...$selectExpressions): ISelectPreFromSelectQueryBuilder {
        return new class([Select::new(... $selectExpressions)]) extends CoreObject implements ISelectPreFromSelectQueryBuilder {
            use SelectQueryBuilderTrait {
                preFromSelect as public select;
                postFromSelect as private;
                where as private;
                andWhere as private;
                orWhere as private;
                groupBy as private;
                having as private;
                andHaving as private;
                orHaving as private;
                orderBy as private;
                limit as private;
                offset as private;
                getQuery as private;
            }
        };
    }
}