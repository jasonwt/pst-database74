<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses;

use Pst\Core\ICoreObject;

use Pst\Database\Query\IQueryable;

interface IClauseExpression extends ICoreObject, IQueryable {
    //public function getExpression();

    public function getIdentifiers(): array;
}