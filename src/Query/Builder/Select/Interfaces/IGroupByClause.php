<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Select\Interfaces;

interface IGroupByClause extends IOrderByClause {
    public function having(... $expressions): IHavingClause;
    public function orderBy(... $columns): IOrderByClause;
}