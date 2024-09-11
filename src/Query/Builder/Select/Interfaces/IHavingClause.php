<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Select\Interfaces;

interface IHavingClause extends IOrderByClause {
    /**
     * 
     * @param string|ColumnIdentifier ...$columns 
     * @return IOrderByClause 
     */
    public function orderBy(...$columns): IOrderByClause;
    public function and(...$expressions): IHavingClause;
    public function or(...$expressions): IHavingClause;
}