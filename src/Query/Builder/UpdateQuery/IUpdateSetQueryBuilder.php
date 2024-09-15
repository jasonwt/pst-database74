<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\UpdateQuery;

use Pst\Database\Query\Builder\IQueryBuilder;

interface IUpdateSetQueryBuilder extends IQueryBuilder {
    public function set(...$setExpressions): IUpdateQueryBuilder;
} 