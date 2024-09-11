<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Select\Interfaces;

interface IWhereClause extends IGroupByClause {
    /**
     * @param string|ColumnIdentifier ...$columns
     * 
     * @return IGroupByClause
     */
    public function groupBy(string ...$columns): IGroupByClause;
    public function and(...$expressions): IWhereClause;
    public function or(...$expressions): IWhereClause;
}