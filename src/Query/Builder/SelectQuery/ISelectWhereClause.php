<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Database\Query\Builder\Clauses\Where\IWhereExpression;
use Pst\Database\Query\Builder\Clauses\GroupBy\IGroupByExpression;

interface ISelectWhereClause extends ISelectGroupByClause {
    // /**
    //  * adds an OR condition to the select where clause
    //  * 
    //  * @param ...string|IWhereExpression $expressions
    //  * 
    //  * @return ISelectWhereClause 
    //  */
    // public function or(... $expressions): ISelectWhereClause;

    // /**
    //  * adds an AND condition to the select where clause
    //  * 
    //  * @param ...string|IWhereExpression $expressions
    //  * 
    //  * @return ISelectWhereClause 
    //  */
    // public function and(... $expressions): ISelectWhereClause;

    // /**
    //  * adds an ORDER BY clause to the select query
    //  * 
    //  * @param ...string|IgroupByExpression $expressions
    //  * 
    //  * @return ISelectgroupByClause 
    //  */
    // public function groupBy(... $expressions): ISelectGroupByClause;
}