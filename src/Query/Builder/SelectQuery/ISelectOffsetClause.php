<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Database\Query\IQuery;
use Pst\Database\Query\Builder\IAutoJoiner;

interface ISelectOffsetClause {
    /**
     * Constructs and returns the select query
     * 
     * @param null|IAutoJoiner $autoJoiner
     * 
     * @return IQuery
     */
    //public function getQuery(?IAutoJoiner $autoJoiner = null): IQuery;
}