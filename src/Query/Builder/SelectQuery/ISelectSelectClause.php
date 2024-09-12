<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Database\Query\Builder\Clauses\From\IFromExpression;

interface ISelectSelectClause {
    /**
     * Set the select of a select query
     * 
     * @param string|IFromExpression|TableIdentifier ...$expressions
     * 
     * @return IFromClause
     */
    public function from(...$expressions): ISelectJoinClause;
}