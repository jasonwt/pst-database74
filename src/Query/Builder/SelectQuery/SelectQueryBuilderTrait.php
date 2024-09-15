<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Core\CoreObject;

use Pst\Database\Enums\JoinType;
use Pst\Database\Query\Builder\QueryBuilderTrait;
use Pst\Database\Query\Builder\Clauses\From\From;
use Pst\Database\Query\Builder\Clauses\Limit\Limit;
use Pst\Database\Query\Builder\Clauses\Where\Where;
use Pst\Database\Query\Builder\Clauses\Offset\Offset;
use Pst\Database\Query\Builder\Clauses\Select\Select;
use Pst\Database\Query\Builder\Clauses\Having\Having;
use Pst\Database\Query\Builder\Clauses\GroupBy\GroupBy;
use Pst\Database\Query\Builder\Clauses\OrderBy\OrderBy;
use Pst\Database\Query\Builder\Clauses\Join\Join;
use Pst\Database\Query\Builder\Clauses\Join\JoinExpression;
use Pst\Database\Query\Builder\Clauses\Join\IJoinExpression;

use InvalidArgumentException;

trait SelectQueryBuilderTrait {
    use QueryBuilderTrait;

    /**
     * Validates the query
     * 
     * @return void 
     * 
     * @throws InvalidArgumentException 
     */
    protected function validateQuery(): void {
        if ($this->queryBuilderTraitClauses[Select::class] === null) {
            throw new InvalidArgumentException("Select not set");
        }

        if ($this->queryBuilderTraitClauses[From::class] === null) {
            throw new InvalidArgumentException("From not set");
        }
    }

    /**
     * Add select expressions to the query (before the from clause has been called)
     * 
     * @param mixed ...$selectExpressions The select expressions
     * 
     * @return ISelectPreFromSelectQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    private function preFromSelect(...$selectExpressions): ISelectPreFromSelectQueryBuilder {
        if (count($selectExpressions) === 0) {
            throw new InvalidArgumentException("No select expressions provided");
        }

        if ($this->queryBuilderTraitClauses[Select::class] !== null) {
            $selectExpressions = array_merge($this->queryBuilderTraitClauses[Select::class]->getExpressions(), $selectExpressions);
        }

        $this->queryBuilderTraitClauses[Select::class] = Select::new(...$selectExpressions);

        return $this;
    }

    /**
     * Add select expressions to the query (after the from clause has been called)
     * 
     * @param mixed ...$selectExpressions The select expressions
     * 
     * @return ISelectQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    private function postFromSelect(...$selectExpressions): ISelectQueryBuilder {
        if (count($selectExpressions) === 0) {
            throw new InvalidArgumentException("No select expressions provided");
        }

        $this->queryBuilderTraitClauses[Select::class] = Select::new(...array_merge($this->queryBuilderTraitClauses[Select::class]->getExpressions(), $selectExpressions));

        return new class(array_filter($this->queryBuilderTraitClauses, fn($v) => !empty($v))) extends CoreObject implements ISelectQueryBuilder {
            use SelectQueryBuilderTrait {
                postFromSelect as public select;
            }
        };
    }

    /**
     * Add from expressions to the query
     * 
     * @param mixed ...$fromExpressions The from expressions
     * 
     * @return ISelectQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function from(... $fromExpressions): ISelectQueryBuilder {
        if (count($fromExpressions) === 0) {
            throw new InvalidArgumentException("No from expressions provided");
        }

        if ($this->queryBuilderTraitClauses[From::class] !== null) {
            $this->queryBuilderTraitClauses[From::class] = From::new(... array_merge($this->queryBuilderTraitClauses[From::class]->getExpressions(), $fromExpressions));
        } else {
            $this->queryBuilderTraitClauses[From::class] = From::new(...$fromExpressions);
        }

        return new class(array_filter($this->queryBuilderTraitClauses, fn($v) => !empty($v))) extends CoreObject implements ISelectQueryBuilder {
            use SelectQueryBuilderTrait {
                postFromSelect as public select;
            }
        };
    }

    /**
     * Add select join expressions to the query
     * 
     * @param JoinType $joinType The join type
     * @param mixed ...$joinExpressions The join expressions
     * 
     * @return ISelectQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    private function addJoin(JoinType $joinType, ...$joinExpressions): ISelectQueryBuilder {
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
     * @return ISelectQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function join(...$innerJoinExpressions): ISelectQueryBuilder {
        return $this->addJoin(JoinType::INNER(), ...$innerJoinExpressions);
    }

    /**
     * Add inner join expressions to the query
     * 
     * @param mixed ...$innerJoinExpressions The inner join expressions
     * 
     * @return ISelectQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function innerJoin(...$innerJoinExpressions): ISelectQueryBuilder {
        return $this->addJoin(JoinType::INNER(), ...$innerJoinExpressions);
    }

    /**
     * Add left join expressions to the query
     * 
     * @param mixed ...$leftJoinExpressions The left join expressions
     * 
     * @return ISelectQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function leftJoin(...$leftJoinExpressions): ISelectQueryBuilder {
        return $this->addJoin(JoinType::LEFT(), ...$leftJoinExpressions);
    }

    /**
     * Add right join expressions to the query
     * 
     * @param mixed ...$rightJoinExpressions The right join expressions
     * 
     * @return ISelectQueryBuilder
     * 
     * @throws InvalidArgumentException
     */
    public function rightJoin(...$rightJoinExpressions): ISelectQueryBuilder {
        return $this->addJoin(JoinType::RIGHT(), ...$rightJoinExpressions);
    }

    /**
     * Add where expressions to the query
     * 
     * @param string|IWhereExpression $whereExpressions 
     * 
     * @return ISelectQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function where($whereExpression): ISelectQueryBuilder {
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
     * @return ISelectQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function andWhere($andWhereExpression): ISelectQueryBuilder {
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
     * @return ISelectQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function orWhere($orWhereExpression): ISelectQueryBuilder {
        if ($this->queryBuilderTraitClauses[Where::class] === null) {
            throw new InvalidArgumentException("Where not set.  Please use where before using orWhere");
        } else {
            $this->queryBuilderTraitClauses[Where::class] = $this->queryBuilderTraitClauses[Where::class]->or($orWhereExpression);
        }

        return $this;
    }

    /**
     * Add group by expressions to the query
     * 
     * @param mixed ...$groupByExpressions 
     * 
     * @return ISelectQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function groupBy(...$groupByExpressions): ISelectQueryBuilder {
        if (count($groupByExpressions) === 0) {
            throw new InvalidArgumentException("No group by expressions provided");
        }

        if ($this->queryBuilderTraitClauses[GroupBy::class] !== null) {
            $this->queryBuilderTraitClauses[GroupBy::class] = GroupBy::new(... array_merge($this->queryBuilderTraitClauses[GroupBy::class]->getExpressions(), $groupByExpressions));
        } else {
            $this->queryBuilderTraitClauses[GroupBy::class] = GroupBy::new(...$groupByExpressions);
        }

        return $this;
    }

    /**
     * Add having expressions to the query
     * 
     * @param mixed ...$havingExpressions 
     * 
     * @return ISelectQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function having(...$havingExpressions): ISelectQueryBuilder {
        if (count($havingExpressions) === 0) {
            throw new InvalidArgumentException("No having expressions provided");
        }

        if ($this->queryBuilderTraitClauses[Having::class] !== null) {
            throw new InvalidArgumentException("Having already set.  Please use andHaving or orHaving");
        } else {
            $this->queryBuilderTraitClauses[Having::class] = Having::new(...$havingExpressions);
        }

        return $this;
    }

    /**
     * Add and having expressions to the query
     * 
     * @param mixed ...$andHavingExpressions 
     * 
     * @return ISelectQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function andHaving(...$andHavingExpressions): ISelectQueryBuilder {
        if (count($andHavingExpressions) === 0) {
            throw new InvalidArgumentException("No and having expressions provided");
        }

        if ($this->queryBuilderTraitClauses[Having::class] === null) {
            throw new InvalidArgumentException("Having not set.  Please use having before using andHaving");
        } else {
            $this->queryBuilderTraitClauses[Having::class] = $this->queryBuilderTraitClauses[Having::class]->and(...$andHavingExpressions);
        }

        return $this;
    }

    /**
     * Add or having expressions to the query
     * 
     * @param mixed ...$orHavingExpressions 
     * 
     * @return ISelectQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function orHaving(...$orHavingExpressions): ISelectQueryBuilder {
        if (count($orHavingExpressions) === 0) {
            throw new InvalidArgumentException("No or having expressions provided");
        }

        if ($this->queryBuilderTraitClauses[Having::class] === null) {
            throw new InvalidArgumentException("Having not set.  Please use having before using orHaving");
        } else {
            $this->queryBuilderTraitClauses[Having::class] = $this->queryBuilderTraitClauses[Having::class]->or(...$orHavingExpressions);
        }

        return $this;
    }

    /**
     * Add order by expressions to the query
     * 
     * @param mixed ...$orderByExpressions 
     * 
     * @return ISelectQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function orderBy(...$orderByExpressions): ISelectQueryBuilder {
        if (count($orderByExpressions) === 0) {
            throw new InvalidArgumentException("No order by expressions provided");
        }

        if ($this->queryBuilderTraitClauses[OrderBy::class] !== null) {
            $this->queryBuilderTraitClauses[OrderBy::class] = OrderBy::new(... array_merge($this->queryBuilderTraitClauses[OrderBy::class]->getExpressions(), $orderByExpressions));
        } else {
            $this->queryBuilderTraitClauses[OrderBy::class] = OrderBy::new(...$orderByExpressions);
        }

        return $this;
    }

    /**
     * Add limit to the query
     * 
     * @param int $limit The limit
     * 
     * @return ISelectQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function limit(int $limit): ISelectQueryBuilder {
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

    /**
     * Add offset to the query
     * 
     * @param int $offset The offset
     * 
     * @return ISelectQueryBuilder 
     * 
     * @throws InvalidArgumentException 
     */
    public function offset(int $offset): ISelectQueryBuilder {
        if ($this->queryBuilderTraitClauses[Offset::class] !== null) {
            throw new InvalidArgumentException("Offset already set");
        }

        if ($this->queryBuilderTraitClauses[Offset::class] !== null) {
            throw new InvalidArgumentException("Offset already set");
        } else {
            $this->queryBuilderTraitClauses[Offset::class] = Offset::new($offset);
        }

        return $this;
    }
}