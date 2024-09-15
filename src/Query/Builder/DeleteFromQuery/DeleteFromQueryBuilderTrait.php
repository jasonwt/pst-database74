<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\DeleteFromQuery;

use Pst\Database\Query\Builder\QueryBuilderTrait;

use Pst\Database\Enums\JoinType;
use Pst\Database\Query\Builder\Clauses\Where\Where;
use Pst\Database\Query\Builder\Clauses\Limit\Limit;
use Pst\Database\Query\Builder\Clauses\Join\Join;
use Pst\Database\Query\Builder\Clauses\Join\IJoinExpression;
use Pst\Database\Query\Builder\Clauses\Join\JoinExpression;

use InvalidArgumentException;

trait DeleteFromQueryBuilderTrait {
    use QueryBuilderTrait;

    /**
     * Validates the query
     * 
     * @return void 
     * 
     * @throws InvalidArgumentException 
     */
    protected function validateQuery(): void {
    }

    // public function on(...$onExpressions): IDeleteFromQueryBuilder {
    //     throw new NotImplementedException();
    // }


    /**
     * Add select join expressions to the query
     * 
     * @param JoinType $joinType The join type
     * @param mixed ...$joinExpressions The join expressions
     * 
     * @return IDeleteFromQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    private function addJoin(JoinType $joinType, ...$joinExpressions): IDeleteFromQueryBuilder {
        if (count($joinExpressions) === 0) {
            throw new InvalidArgumentException("No join expressions provided");
        }

        $joinExpressions = array_map(function($expression) use ($joinType) {
            if (($expression = Join::tryConstructExpression($expression)) === null) {
                throw new InvalidArgumentException("Invalid join expression");
            }

            return new class((string) $joinType, $expression) extends JoinExpression implements IJoinExpression {
                private string $joinType;

                public function __construct(string $joinType, IJoinExpression $expression) {
                    $this->joinType = $joinType;
                    parent::__construct($expression);
                }
                public function getQuerySql(): string {
                    return $this->joinType . " JOIN " . $this->getExpression()->getQuerySql();
                }
            };
        }, $joinExpressions);

        if ($this->queryBuilderTraitClauses[Join::class] !== null) {
            $this->queryBuilderTraitClauses[Join::class] = Join::new(... array_merge($this->queryBuilderTraitClauses[Join::class]->getExpressions(), $joinExpressions));
        } else {
            $this->queryBuilderTraitClauses[Join::class] = Join::new(...$joinExpressions);
        }

        return $this;
    }

    /**
     * Add join expressions to the query
     * 
     * @param mixed ...$innerJoinExpressions The join expressions
     * 
     * @return IDeleteFromQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function join(...$innerJoinExpressions): IDeleteFromQueryBuilder {
        return $this->addJoin(JoinType::INNER(), ...$innerJoinExpressions);
    }

    /**
     * Add inner join expressions to the query
     * 
     * @param mixed ...$innerJoinExpressions The inner join expressions
     * 
     * @return IDeleteFromQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function innerJoin(...$innerJoinExpressions): IDeleteFromQueryBuilder {
        return $this->addJoin(JoinType::INNER(), ...$innerJoinExpressions);
    }

    /**
     * Add left join expressions to the query
     * 
     * @param mixed ...$leftJoinExpressions The left join expressions
     * 
     * @return IDeleteFromQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function leftJoin(...$leftJoinExpressions): IDeleteFromQueryBuilder {
        return $this->addJoin(JoinType::LEFT(), ...$leftJoinExpressions);
    }

    /**
     * Add right join expressions to the query
     * 
     * @param mixed ...$rightJoinExpressions The right join expressions
     * 
     * @return IDeleteFromQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function rightJoin(...$rightJoinExpressions): IDeleteFromQueryBuilder {
        return $this->addJoin(JoinType::RIGHT(), ...$rightJoinExpressions);
    }

    /**
     * Add where expressions to the query
     * 
     * @param string|IWhereExpression $whereExpressions 
     * 
     * @return IDeleteFromQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function where($whereExpression): IDeleteFromQueryBuilder {
        if ($this->queryBuilderTraitClauses[Where::class] !== null) {
            throw new InvalidArgumentException("Where already set.  Please use andWhere or orWhere");
        } else {
            $this->queryBuilderTraitClauses[Where::class] = Where::new($whereExpression);
        }

        return $this;
    }

    /**
     * Add and where expressions to the query
     * 
     * @param string|IWhereExpression $andWhereExpression
     * 
     * @return IDeleteFromQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function andWhere($andWhereExpression): IDeleteFromQueryBuilder {
        if ($this->queryBuilderTraitClauses[Where::class] === null) {
            throw new InvalidArgumentException("Where not set.  Please use where before using andWhere");
        } else {
            $this->queryBuilderTraitClauses[Where::class] = $this->queryBuilderTraitClauses[Where::class]->and($andWhereExpression);
        }

        return $this;
    }

    /**
     * Add or where expressions to the query
     * 
     * @param string|IWhereExpression $orWhereExpression 
     * 
     * @return IDeleteFromQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function orWhere($orWhereExpression): IDeleteFromQueryBuilder {
        if ($this->queryBuilderTraitClauses[Where::class] === null) {
            throw new InvalidArgumentException("Where not set.  Please use where before using orWhere");
        } else {
            $this->queryBuilderTraitClauses[Where::class] = $this->queryBuilderTraitClauses[Where::class]->or($orWhereExpression);
        }

        return $this;
    }

    /**
     * Add limit to the query
     * 
     * @param int $limit The limit
     * 
     * @return IDeleteFromQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function limit(int $limit): IDeleteFromQueryBuilder {
        if ($this->queryBuilderTraitClauses[Limit::class] !== null) {
            throw new InvalidArgumentException("Limit already set");
        }

        if ($this->queryBuilderTraitClauses[Limit::class] !== null) {
            throw new InvalidArgumentException("Limit already set");
        } else {
            $this->queryBuilderTraitClauses[Limit::class] = Limit::new($limit);
        }

        return $this;
    }
}