<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Select\Interfaces;

interface IJoinClause extends IGroupByClause {
    /**
     * @param string|ColumnIdentifier ...$columns 
     * 
     * @return IGroupByClause 
     */
    public function groupBy(...$columns): IGroupByClause;

    public function innerJoin(...$expressions): IJoinClause;
    public function outerJoin(...$expressions): IJoinClause;
    public function leftJoin(...$expressions): IJoinClause;
    public function rightJoin(...$expressions): IJoinClause;
    
    public function where(...$expressions): IWhereClause;
}