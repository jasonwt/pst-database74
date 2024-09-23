<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder;

use Pst\Core\Types\Type;
use Pst\Core\Enumerable\Enumerator;
use Pst\Core\Enumerable\IEnumerable;

use Pst\Database\Query\IQuery;

use Pst\Database\Query\Builder\Clauses\IClause;
use Pst\Database\Query\Builder\Clauses\Set\Set;

use Pst\Database\Query\Builder\Clauses\From\From;
use Pst\Database\Query\Builder\Clauses\Join\Join;
use Pst\Database\Query\Builder\Clauses\Limit\Limit;
use Pst\Database\Query\Builder\Clauses\Where\Where;
use Pst\Database\Query\Builder\Clauses\Update\Update;
use Pst\Database\Query\Builder\Clauses\Insert\Insert;
use Pst\Database\Query\Builder\Clauses\Offset\Offset;
use Pst\Database\Query\Builder\Clauses\Select\Select;
use Pst\Database\Query\Builder\Clauses\Having\Having;
use Pst\Database\Query\Builder\Clauses\GroupBy\GroupBy;
use Pst\Database\Query\Builder\Clauses\OrderBy\OrderBy;
use Pst\Database\Query\Builder\Clauses\DeleteFrom\DeleteFrom;
use Pst\Database\Query\Builder\Clauses\ReplaceInto\ReplaceInto;

use Pst\Core\Exceptions\NotImplementedException;

use InvalidArgumentException;



trait QueryBuilderTrait {
    private array $queryBuilderTraitClauses = [
        Insert::class => null,
        ReplaceInto::class => null,
        Update::class => null,
        Set::class => null,
        DeleteFrom::class => null,
        Select::class => null,
        From::class => null,
        Join::class => null,
        Where::class => null,
        GroupBy::class => null,
        Having::class => null,
        OrderBy::class => null,
        Limit::class => null,
        Offset::class => null
    ];

    public function __construct(array $clauses = []) {
        foreach ($clauses as $clauseName => $clauseValue) {
            if (!is_object($clauseValue)) {
                throw new InvalidArgumentException("Invalid clause value: $clauseValue");
            }

            $thisClauseClass = null;

            $clauseValueClass = get_class($clauseValue);
            
            if (array_key_exists($clauseValueClass, $this->queryBuilderTraitClauses)) {
                $thisClauseClass = $clauseValueClass;
            } else {
                foreach ($this->queryBuilderTraitClauses as $clauseClass => $clause) {
                    if (is_a($clauseValue, $clauseClass, true)) {
                        $thisClauseClass = $clauseClass;
                        break;
                    }
                }
            }

            if ($thisClauseClass === null) {
                throw new InvalidArgumentException("Invalid clause value: $clauseValue");
            }

            if ($this->queryBuilderTraitClauses[$thisClauseClass] !== null) {
                throw new InvalidArgumentException("Clause already set: $thisClauseClass");
            }

            $this->queryBuilderTraitClauses[$thisClauseClass] = $clauseValue;
        }
    }

    public function getClause(string $clauseClassName): ?IClause {
        if (!array_key_exists($clauseClassName, $this->queryBuilderTraitClauses)) {
            if (!class_exists($clauseClassName) || !is_a($clauseClassName, IClause::class, true)) {
                throw new InvalidArgumentException("Invalid clause class: $clauseClassName");
            }
            
            return null;
        }

        return $this->queryBuilderTraitClauses[$clauseClassName];
    }

    public function getClauses(): IEnumerable {
        return Enumerator::new($this->queryBuilderTraitClauses, Type::interface(IClause::class));
    }

    public function getIdentifiers(): array {
        $identifiers = [];

        foreach ($this->queryBuilderTraitClauses as $clauseClassName => $clause) {
            if ($clause === null) {
                continue;
            }

            $clauseIdentifiers = [
                "schemas" => [],
                "tables" => [],
                "columns" => [],
                "aliases" => []
            ];

            foreach ($clause->getIdentifiers() as $key => $value) {
                $clauseIdentifiers[$key] += $value;
            }

            if (Enumerator::new($clauseIdentifiers)->any(fn($v) => !empty($v))) {
                $identifiers[$clauseClassName] = Enumerator::new($clauseIdentifiers)->where(fn($v, $k) => !empty($v))->toArray();
            }
        }
        
        return $identifiers;
    }

    protected abstract function validateQuery(): void;

    /**
     * Gets the query
     * 
     * @param null|AutoJoiner $autoJoiner
     * 
     * @return IQuery 
     */
    public function getQuery(): IQuery {
        $querySql = "";
        $queryParameters = [];

        $this->validateQuery();

        foreach ($this->queryBuilderTraitClauses as $clauseClass => $clause) {
            if ($clause === null) {
                continue;
            }

            $querySql .= ltrim($clauseClass::getBeginClauseStatement() . " " . rtrim($clause->getQuerySql()) . "\n");

            $queryParameters += $clause->getQueryParameters();
        }

        return new class($querySql, $queryParameters) implements IQuery {
            private string $querySql;
            private array $queryParameters;

            public function __construct(string $querySql, array $queryParameters) {
                $this->querySql = $querySql;
                $this->queryParameters = $queryParameters;
            }

            public function getSql(): string {
                return $this->querySql;
            }

            public function getParameters(): array {
                return $this->queryParameters;
            }

            public function getParameterlessQuery(): string {
                throw new NotImplementedException();
            }
        };
    }
}