<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Select\Interfaces;

interface ILimitClause extends IOffsetClause{
    public function offset(int $offset): IOffsetClause;
}