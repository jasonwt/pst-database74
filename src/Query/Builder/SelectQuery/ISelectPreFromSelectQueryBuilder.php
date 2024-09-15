<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Database\Query\Builder\IQueryBuilder;

interface ISelectPreFromSelectQueryBuilder extends IQueryBuilder {
    public function select(...$expressions): ISelectPreFromSelectQueryBuilder;
    public function from(...$expressions): ISelectQueryBuilder;
}