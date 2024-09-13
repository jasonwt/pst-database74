<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\SelectQuery;

use Pst\Core\CoreObject;

use Pst\Database\Query\Builder\QueryBuilderTrait;
use Pst\Database\Query\Builder\Clauses\From\From;
use Pst\Database\Query\Builder\Clauses\Join\Join;
use Pst\Database\Query\Builder\Clauses\Limit\Limit;
use Pst\Database\Query\Builder\Clauses\Where\Where;
use Pst\Database\Query\Builder\Clauses\Offset\Offset;
use Pst\Database\Query\Builder\Clauses\Select\Select;
use Pst\Database\Query\Builder\Clauses\Having\Having;
use Pst\Database\Query\Builder\Clauses\GroupBy\GroupBy;
use Pst\Database\Query\Builder\Clauses\OrderBy\OrderBy;

use InvalidArgumentException;

trait SelectQueryBuilderTrait {
    use QueryBuilderTrait;

    protected function validateQuery(): void {
        if ($this->selectQueryTraitClauses[Select::class] === null) {
            throw new InvalidArgumentException("Select not set");
        }

        if ($this->selectQueryTraitClauses[From::class] === null) {
            throw new InvalidArgumentException("From not set");
        }
    }

    private function preFromSelect(...$selectExpressions): ISelectPreFromSelectQueryBuilder {
        if (count($selectExpressions) === 0) {
            throw new InvalidArgumentException("No select expressions provided");
        }

        if ($this->selectQueryTraitClauses[Select::class] !== null) {
            $selectExpressions = array_merge($this->selectQueryTraitClauses[Select::class]->getExpressions(), $selectExpressions);
        }

        $this->selectQueryTraitClauses[Select::class] = Select::new(...$selectExpressions);

        return $this;
    }

    private function postFromSelect(...$selectExpressions): ISelectQueryBuilder {
        if (count($selectExpressions) === 0) {
            throw new InvalidArgumentException("No select expressions provided");
        }

        $this->selectQueryTraitClauses[Select::class] = Select::new(...array_merge($this->selectQueryTraitClauses[Select::class]->getExpressions(), $selectExpressions));

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements ISelectQueryBuilder {
            use SelectQueryBuilderTrait {
                postFromSelect as public select;
            }
        };
    }

    public function from(... $fromExpressions): ISelectQueryBuilder {
        if (count($fromExpressions) === 0) {
            throw new InvalidArgumentException("No from expressions provided");
        }

        if ($this->selectQueryTraitClauses[From::class] !== null) {
            $this->selectQueryTraitClauses[From::class] = From::new(... array_merge($this->selectQueryTraitClauses[From::class]->getExpressions(), $fromExpressions));
        } else {
            $this->selectQueryTraitClauses[From::class] = From::new(...$fromExpressions);
        }

        return new class(array_filter($this->selectQueryTraitClauses, fn($v) => !empty($v))) extends CoreObject implements ISelectQueryBuilder {
            use SelectQueryBuilderTrait {
                postFromSelect as public select;
            }
        };
    }

    public function where(...$whereExpressions): ISelectQueryBuilder {
        if (count($whereExpressions) === 0) {
            throw new InvalidArgumentException("No where expressions provided");
        }

        if ($this->selectQueryTraitClauses[Where::class] !== null) {
            throw new InvalidArgumentException("Where already set.  Please use andWhere or orWhere");
        } else {
            $this->selectQueryTraitClauses[Where::class] = Where::new(...$whereExpressions);
        }

        return $this;
    }

    public function andWhere(...$andWhereExpressions): ISelectQueryBuilder {
        if (count($andWhereExpressions) === 0) {
            throw new InvalidArgumentException("No and where expressions provided");
        }

        if ($this->selectQueryTraitClauses[Where::class] === null) {
            throw new InvalidArgumentException("Where not set.  Please use where before using andWhere");
        } else {
            $this->selectQueryTraitClauses[Where::class] = $this->selectQueryTraitClauses[Where::class]->and(...$andWhereExpressions);
        }

        return $this;
    }

    public function orWhere(...$orWhereExpressions): ISelectQueryBuilder {
        if (count($orWhereExpressions) === 0) {
            throw new InvalidArgumentException("No or where expressions provided");
        }

        if ($this->selectQueryTraitClauses[Where::class] === null) {
            throw new InvalidArgumentException("Where not set.  Please use where before using orWhere");
        } else {
            $this->selectQueryTraitClauses[Where::class] = $this->selectQueryTraitClauses[Where::class]->or(...$orWhereExpressions);
        }

        return $this;
    }

    public function groupBy(...$groupByExpressions): ISelectQueryBuilder {
        if (count($groupByExpressions) === 0) {
            throw new InvalidArgumentException("No group by expressions provided");
        }

        if ($this->selectQueryTraitClauses[GroupBy::class] !== null) {
            $this->selectQueryTraitClauses[GroupBy::class] = GroupBy::new(... array_merge($this->selectQueryTraitClauses[GroupBy::class]->getExpressions(), $groupByExpressions));
        } else {
            $this->selectQueryTraitClauses[GroupBy::class] = GroupBy::new(...$groupByExpressions);
        }

        return $this;
    }

    public function having(...$havingExpressions): ISelectQueryBuilder {
        if (count($havingExpressions) === 0) {
            throw new InvalidArgumentException("No having expressions provided");
        }

        if ($this->selectQueryTraitClauses[Having::class] !== null) {
            throw new InvalidArgumentException("Having already set.  Please use andHaving or orHaving");
        } else {
            $this->selectQueryTraitClauses[Having::class] = Having::new(...$havingExpressions);
        }

        return $this;
    }

    public function andHaving(...$andHavingExpressions): ISelectQueryBuilder {
        if (count($andHavingExpressions) === 0) {
            throw new InvalidArgumentException("No and having expressions provided");
        }

        if ($this->selectQueryTraitClauses[Having::class] === null) {
            throw new InvalidArgumentException("Having not set.  Please use having before using andHaving");
        } else {
            $this->selectQueryTraitClauses[Having::class] = $this->selectQueryTraitClauses[Having::class]->and(...$andHavingExpressions);
        }

        return $this;
    }

    public function orHaving(...$orHavingExpressions): ISelectQueryBuilder {
        if (count($orHavingExpressions) === 0) {
            throw new InvalidArgumentException("No or having expressions provided");
        }

        if ($this->selectQueryTraitClauses[Having::class] === null) {
            throw new InvalidArgumentException("Having not set.  Please use having before using orHaving");
        } else {
            $this->selectQueryTraitClauses[Having::class] = $this->selectQueryTraitClauses[Having::class]->or(...$orHavingExpressions);
        }

        return $this;
    }

    public function orderBy(...$orderByExpressions): ISelectQueryBuilder {
        if (count($orderByExpressions) === 0) {
            throw new InvalidArgumentException("No order by expressions provided");
        }

        if ($this->selectQueryTraitClauses[OrderBy::class] !== null) {
            $this->selectQueryTraitClauses[OrderBy::class] = OrderBy::new(... array_merge($this->selectQueryTraitClauses[OrderBy::class]->getExpressions(), $orderByExpressions));
        } else {
            $this->selectQueryTraitClauses[OrderBy::class] = OrderBy::new(...$orderByExpressions);
        }

        return $this;
    }

    public function limit(int $limit): ISelectQueryBuilder {
        if ($this->selectQueryTraitClauses[Limit::class] !== null) {
            throw new InvalidArgumentException("Limit already set");
        }

        if ($this->selectQueryTraitClauses[Limit::class] !== null) {
            throw new InvalidArgumentException("Limit already set");
        } else {
            $this->selectQueryTraitClauses[Limit::class] = Limit::new($limit);
        }

        return $this;
    }

    public function offset(int $offset): ISelectQueryBuilder {
        if ($this->selectQueryTraitClauses[Offset::class] !== null) {
            throw new InvalidArgumentException("Offset already set");
        }

        if ($this->selectQueryTraitClauses[Offset::class] !== null) {
            throw new InvalidArgumentException("Offset already set");
        } else {
            $this->selectQueryTraitClauses[Offset::class] = Offset::new($offset);
        }

        return $this;
    }
}