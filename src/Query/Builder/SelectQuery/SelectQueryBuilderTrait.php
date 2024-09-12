<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Core\CoreObject;

use Pst\Database\Query\Builder\CoreQueryBuilderTrait;
use Pst\Database\Query\Builder\Clauses\From\From;
use Pst\Database\Query\Builder\Clauses\Where\Where;

use InvalidArgumentException;

trait SelectQueryBuilderTrait {
    use CoreQueryBuilderTrait;

    private function from(...$tables): ISelectJoinClause {
        if (count($tables) === 0) {
            throw new InvalidArgumentException("No tables provided");
        }
        
        $this->selectQueryTraitClauses[From::class] = From::new(...$tables);

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements ISelectJoinClause {
            use SelectQueryBuilderTrait {
                innerJoin as public;
                outerJoin as public;
                leftJoin as public;
                rightJoin as public;
                fullJoin as public;
                where as public;
                groupBy as public;
                having as public;
                orderBy as public;
                limit as public;
                offset as public;
                getQuery as public;
            }
        };
    }

    private function innerJoin(...$expressions): ISelectJoinClause {
        return $this;
    }

    private function outerJoin(...$expressions): ISelectJoinClause {
        return $this;
    }

    private function leftJoin(...$expressions): ISelectJoinClause {
        return $this;
    }

    private function rightJoin(...$expressions): ISelectJoinClause {
        return $this;
    }

    private function fullJoin(...$expressions): ISelectJoinClause {
        return $this;
    }

    private function where(...$expressions): ISelectWhereClause {
        if (count($expressions) === 0) {
            throw new InvalidArgumentException("No expressions provided");
        }
        
        $this->selectQueryTraitClauses[Where::class] = Where::new(...$expressions);

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements ISelectWhereClause {
            use SelectQueryBuilderTrait {
                andWhere as public and;
                orWhere as public or;
                groupBy as public;
                having as public;
                andHaving as private;
                orHaving as private;
                orderBy as public;
                limit as public;
                offset as public;
                getQuery as private;
            }
        };
    }

    private function andWhere(...$expressions): ISelectWhereClause {
        return $this;
    }

    private function orWhere(...$expressions): ISelectWhereClause {
        return $this;
    }

    private function groupBy(...$expressions): ISelectGroupByClause {
        return $this;
    }

    private function having(...$expressions): ISelectHavingClause {
        return $this;
    }

    private function andHaving(...$expressions): ISelectHavingClause {
        return $this;
    }

    private function orHaving(...$expressions): ISelectHavingClause {
        return $this;
    }

    private function orderBy(...$expressions): ISelectOrderByClause {
        return $this;
    }

    private function limit(int $limit): ISelectLimitClause {
        return $this;
    }

    private function offset(int $offset): ISelectOffsetClause {
        return $this;
    }
}