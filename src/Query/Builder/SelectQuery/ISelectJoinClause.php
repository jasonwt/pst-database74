<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

interface ISelectJoinClause extends ISelectWhereClause {
    /**
     * Adds a inner join clause to the select query
     * 
     * @param ...string $expressions 
     * 
     * @return ISelectJoinClause 
     */
    public function innerJoin(... $expressions): ISelectJoinClause;

    /**
     * Adds a outer join clause to the select query
     * 
     * @param ...string $expressions 
     * 
     * @return ISelectJoinClause 
     */
    public function outerJoin(... $expressions): ISelectJoinClause;

    /**
     * Adds a left join clause to the select query
     * 
     * @param ...string $expressions 
     * 
     * @return ISelectJoinClause 
     */
    public function leftJoin(... $expressions): ISelectJoinClause;

    /**
     * Adds a right join clause to the select query
     * 
     * @param ...string $expressions 
     * 
     * @return ISelectJoinClause 
     */
    public function rightJoin(... $expressions): ISelectJoinClause;

    /**
     * Adds a full join clause to the select query
     * 
     * @param ...string $expressions 
     * 
     * @return ISelectJoinClause 
     */
    public function fullJoin(... $expressions): ISelectJoinClause;

    /**
     * Adds a where clause to the select query
     * 
     * @param ...string $expressions 
     * 
     * @return ISelectWhereClause 
     */
    public function where(... $expressions): ISelectWhereClause;
}