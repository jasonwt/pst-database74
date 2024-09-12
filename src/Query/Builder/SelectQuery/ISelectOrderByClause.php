<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Database\Query\Builder\Clauses\Limit\ILimitExpression;

interface ISelectOrderByClause extends ISelectLimitClause {
    // /**
    //  * Set the limit of a select query
    //  * 
    //  * @param string|int|ILimitExpression $limit 
    //  * 
    //  * @return ISelectLimitClause 
    //  */
    // public function limit($limit): ISelectLimitClause;
}