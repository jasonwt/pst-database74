<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Database\Query\Builder\Clauses\Having\IHavingExpression;
use Pst\Database\Query\Builder\Clauses\OrderBy\IOrderByExpression;

interface ISelectHavingClause extends ISelectOrderByClause {
    // /**
    //  * adds an OR condition to the select having clause
    //  * 
    //  * @param ...string|IHavingExpression $expressions
    //  * 
    //  * @return ISelectHavingClause 
    //  */
    // public function or(... $expressions): ISelectHavingClause;

    // /**
    //  * adds an AND condition to the select having clause
    //  * 
    //  * @param ...string|IHavingExpression $expressions
    //  * 
    //  * @return ISelectHavingClause 
    //  */
    // public function and(... $expressions): ISelectHavingClause;

    // /**
    //  * adds an ORDER BY clause to the select query
    //  * 
    //  * @param ...string|IOrderByExpression $expressions
    //  * 
    //  * @return ISelectOrderByClause 
    //  */
    // public function orderBy(... $expressions): ISelectOrderByClause;
}