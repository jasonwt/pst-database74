<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder;

use Pst\Core\CoreObject;

use Pst\Database\Query\Builder\Clauses\DeleteFrom\DeleteFrom;
use Pst\Database\Query\Builder\Clauses\Insert\Insert;
use Pst\Database\Query\Builder\Clauses\Insert\InsertType;
use Pst\Database\Query\Builder\Clauses\ReplaceInto\ReplaceInto;
use Pst\Database\Query\Builder\Clauses\Select\Select;
use Pst\Database\Query\Builder\Clauses\Update\Update;
use Pst\Database\Query\Builder\DeleteFromQuery\DeleteFromQueryBuilderTrait;
use Pst\Database\Query\Builder\DeleteFromQuery\IDeleteFromQueryBuilder;
use Pst\Database\Query\Builder\InsertQuery\IInsertSetQueryBuilder;
use Pst\Database\Query\Builder\InsertQuery\InsertQueryBuilderTrait;
use Pst\Database\Query\Builder\ReplaceIntoQuery\IReplaceIntoSetQueryBuilder;
use Pst\Database\Query\Builder\ReplaceIntoQuery\ReplaceIntoQueryBuilderTrait;
use Pst\Database\Query\Builder\SelectQuery\SelectQueryBuilderTrait;
use Pst\Database\Query\Builder\SelectQuery\ISelectPreFromSelectQueryBuilder;
use Pst\Database\Query\Builder\UpdateQuery\IUpdateSetQueryBuilder;
use Pst\Database\Query\Builder\UpdateQuery\UpdateQueryBuilderTrait;

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

    public static function insertInto($intoExpression): IInsertSetQueryBuilder {
        return new class([Insert::class => Insert::new($intoExpression, InsertType::INTO())]) extends CoreObject implements IInsertSetQueryBuilder {
            use InsertQueryBuilderTrait {
                set as public;
                on as private;
                getQuery as private;
            }
        };
    }

    public static function insertIgnore($intoExpression): IInsertSetQueryBuilder {
        return new class([Insert::class => Insert::new($intoExpression, InsertType::IGNORE())]) extends CoreObject implements IInsertSetQueryBuilder {
            use InsertQueryBuilderTrait {
                set as public;
                on as private;
                getQuery as private;
            }
        };
    }

    public static function replaceInto($intoExpression): IReplaceIntoSetQueryBuilder {
        return new class([ReplaceInto::class => ReplaceInto::new($intoExpression)]) extends CoreObject implements IReplaceIntoSetQueryBuilder {
            use ReplaceIntoQueryBuilderTrait {
                set as public;
                on as private;
                getQuery as private;
            }
        };
    }

    public static function update($intoExpression): IUpdateSetQueryBuilder {
        return new class([Update::class => Update::new($intoExpression)]) extends CoreObject implements IUpdateSetQueryBuilder {
            use UpdateQueryBuilderTrait {
                set as public;
                on as private;
                getQuery as private;
            }
        };
    }

    public static function deleteFrom($fromExpression): IDeleteFromQueryBuilder {
        return new class([DeleteFrom::class => DeleteFrom::new($fromExpression)]) extends CoreObject implements IDeleteFromQueryBuilder {
            use DeleteFromQueryBuilderTrait {
                getQuery as public;
            }
        };
    }
}