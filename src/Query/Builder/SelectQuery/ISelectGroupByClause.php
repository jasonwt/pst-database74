<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Database\Query\Builder\Clauses\Having\IHavingExpression;
use Pst\Database\Query\Builder\Clauses\OrderBy\IOrderByExpression;

interface ISelectGroupByClause extends ISelectHavingClause {
    // /**
    //  * adds an HAVING condition to the select query
    //  * 
    //  * @param ...string|IHavingExpression $expressions
    //  * 
    //  * @return ISelectHavingClause 
    //  */
    // public function having(... $expressions): ISelectHavingClause;

    // /**
    //  * adds an ORDER BY clause to the select query
    //  * 
    //  * @param ...string|IOrderByExpression $expressions
    //  * 
    //  * @return ISelectOrderByClause 
    //  */
    // public function orderBy(... $expressions): ISelectOrderByClause;
}