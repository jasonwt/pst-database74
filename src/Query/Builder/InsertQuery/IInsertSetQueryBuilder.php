<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\InsertQuery;

use Pst\Database\Query\Builder\IQueryBuilder;

interface IInsertSetQueryBuilder extends IQueryBuilder {
    public function set(...$setExpressions): IInsertQueryBuilder;
} 