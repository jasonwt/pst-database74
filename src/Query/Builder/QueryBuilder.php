<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder;

use Pst\Core\CoreObject;

use Pst\Database\Query\Builder\Select\SelectQueryBuilderTrait;
use Pst\Database\Query\Builder\Identifiers\ColumnIdentifier;
use Pst\Database\Query\Builder\Clauses\Select\Select;
use Pst\Database\Query\Builder\Clauses\Select\ISelectExpression;
use Pst\Database\Query\Builder\Select\Interfaces\ISelectClause;

final class QueryBuilder {
    private function __construct() {}

    /**
     * Creates a new select query
     * 
     * @param string|ISelectExpression|ColumnIdentifier ...$columns The columns to select
     * 
     * @return ISelectClause The select clause
     */
    public static function select(...$columns): ISelectClause {
        return new class([Select::class => Select::new(...$columns)]) extends CoreObject implements ISelectClause {
            use SelectQueryBuilderTrait {
                from as public;
            }
        };
    }
}