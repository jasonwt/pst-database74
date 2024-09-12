<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Database\Query\Builder\Clauses\Offset\IOffsetExpression;

interface ISelectLimitClause extends ISelectOffsetClause {
    // /**
    //  * Set the offset of a select query
    //  * 
    //  * @param string|int|IOffsetExpression $offset 
    //  * 
    //  * @return ISelectOffsetClause 
    //  */
    // public function offset($offset): ISelectOffsetClause;
}