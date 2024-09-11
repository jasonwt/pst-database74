<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Select\Interfaces;

interface IOrderByClause extends ILimitClause {
    public function limit(int $limit): ILimitClause;
}