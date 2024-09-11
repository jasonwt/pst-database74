<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Select\Interfaces;

use Pst\Core\ICoreObject;

use Pst\Database\Query\Builder\Clauses\From\IFromExpression;

interface ISelectClause extends ICoreObject {
    /**
     * @param string|IFromExpression|TableIdentifier ...$expressions
     * 
     * @return IFromClause
     */
    public function from(...$expressions): IFromClause;
}