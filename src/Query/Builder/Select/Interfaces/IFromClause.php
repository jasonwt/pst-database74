<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Select\Interfaces;

interface IFromClause extends IJoinClause {
    /**
     * @param string|ColumnIdentifier ...$columns 
     * 
     * @return IGroupByClause 
     */
    
    public function where(...$expressions): IWhereClause;
}