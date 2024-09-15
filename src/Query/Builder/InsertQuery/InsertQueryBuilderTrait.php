<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\InsertQuery;

use Pst\Core\CoreObject;

use Pst\Database\Query\Builder\QueryBuilderTrait;
use Pst\Database\Query\Builder\Clauses\Set\Set;

use Pst\Core\Exceptions\NotImplementedException;

use InvalidArgumentException;

trait InsertQueryBuilderTrait {
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

    public function set(...$setExpressions): IInsertQueryBuilder {
        if (count($setExpressions) === 0) {
            throw new InvalidArgumentException("No from expressions provided");
        }

        if ($this->queryBuilderTraitClauses[Set::class] !== null) {
            $this->queryBuilderTraitClauses[Set::class] = Set::new(... array_merge($this->queryBuilderTraitClauses[Set::class]->getExpressions(), $setExpressions));
        } else {
            $this->queryBuilderTraitClauses[Set::class] = Set::new(...$setExpressions);
        }

        return new class(array_filter($this->queryBuilderTraitClauses, fn($v) => !empty($v))) extends CoreObject implements IInsertQueryBuilder {
            use InsertQueryBuilderTrait {
                getQuery as public;
            }
        };
    }

    public function on(...$onExpressions): IInsertQueryBuilder {
        throw new NotImplementedException();
    }
}