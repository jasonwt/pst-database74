<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\UpdateQuery;

use Pst\Core\CoreObject;

use Pst\Database\Enums\JoinType;
use Pst\Database\Query\Builder\QueryBuilderTrait;
use Pst\Database\Query\Builder\Clauses\Set\Set;
use Pst\Database\Query\Builder\Clauses\Limit\Limit;
use Pst\Database\Query\Builder\Clauses\Where\Where;
use Pst\Database\Query\Builder\Clauses\Join\Join;
use Pst\Database\Query\Builder\Clauses\Join\IJoinExpression;
use Pst\Database\Query\Builder\Clauses\Join\JoinExpression;

use Pst\Core\Exceptions\NotImplementedException;

use InvalidArgumentException;

trait UpdateQueryBuilderTrait {
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

    /**
     * Set the set expressions for the query
     * 
     * @param mixed ...$setExpressions The set expressions
     * 
     * @return IUpdateQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function set(...$setExpressions): IUpdateQueryBuilder {
        if (count($setExpressions) === 0) {
            throw new InvalidArgumentException("No from expressions provided");
        }

        if ($this->queryBuilderTraitClauses[Set::class] !== null) {
            $this->queryBuilderTraitClauses[Set::class] = Set::new(... array_merge($this->queryBuilderTraitClauses[Set::class]->getExpressions(), $setExpressions));
        } else {
            $this->queryBuilderTraitClauses[Set::class] = Set::new(...$setExpressions);
        }

        return new class(array_filter($this->queryBuilderTraitClauses, fn($v) => !empty($v))) extends CoreObject implements IUpdateQueryBuilder {
            use UpdateQueryBuilderTrait {
                getQuery as public;
            }
        };
    }

    public function on(...$onExpressions): IUpdateQueryBuilder {
        throw new NotImplementedException();
    }


    /**
     * Add select join expressions to the query
     * 
     * @param JoinType $joinType The join type
     * @param mixed ...$joinExpressions The join expressions
     * 
     * @return IUpdateQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    private function addJoin(JoinType $joinType, ...$joinExpressions): IUpdateQueryBuilder {
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
     * @return IUpdateQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function join(...$innerJoinExpressions): IUpdateQueryBuilder {
        return $this->addJoin(JoinType::INNER(), ...$innerJoinExpressions);
    }

    /**
     * Add inner join expressions to the query
     * 
     * @param mixed ...$innerJoinExpressions The inner join expressions
     * 
     * @return IUpdateQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function innerJoin(...$innerJoinExpressions): IUpdateQueryBuilder {
        return $this->addJoin(JoinType::INNER(), ...$innerJoinExpressions);
    }

    /**
     * Add left join expressions to the query
     * 
     * @param mixed ...$leftJoinExpressions The left join expressions
     * 
     * @return IUpdateQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function leftJoin(...$leftJoinExpressions): IUpdateQueryBuilder {
        return $this->addJoin(JoinType::LEFT(), ...$leftJoinExpressions);
    }

    /**
     * Add right join expressions to the query
     * 
     * @param mixed ...$rightJoinExpressions The right join expressions
     * 
     * @return IUpdateQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function rightJoin(...$rightJoinExpressions): IUpdateQueryBuilder {
        return $this->addJoin(JoinType::RIGHT(), ...$rightJoinExpressions);
    }


    /**
     * Add where expressions to the query
     * 
     * @param string|IWhereExpression $whereExpressions 
     * 
     * @return IUpdateQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function where($whereExpression): IUpdateQueryBuilder {
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
     * @return IUpdateQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function andWhere($andWhereExpression): IUpdateQueryBuilder {
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
     * @return IUpdateQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function orWhere($orWhereExpression): IUpdateQueryBuilder {
        if ($this->queryBuilderTraitClauses[Where::class] === null) {
            throw new InvalidArgumentException("Where not set.  Please use where before using orWhere");
        } else {
            $this->queryBuilderTraitClauses[Where::class] = $this->queryBuilderTraitClauses[Where::class]->or($orWhereExpression);
        }

        return $this;
    }

    /**
     * Add limit expressions to the query
     * 
     * @param int $limit The limit
     * 
     * @return IUpdateQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function limit(int $limit): IUpdateQueryBuilder {
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