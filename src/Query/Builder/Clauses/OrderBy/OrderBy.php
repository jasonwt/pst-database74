<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\OrderBy;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;

use Pst\Database\PregPatterns;
use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Identifiers\ColumnIdentifier;
use Pst\Database\Query\Builder\Clauses\Traits\ExpressionsTrait;
use Pst\Database\Query\Builder\Identifiers\IColumnIdentifier;
use Pst\Database\Query\Builder\IGetQueryParts;

class OrderBy extends Clause implements IOrderBy {
    use ExpressionsTrait;


    public function getQuerySql(): string {
        return $this->querySql ??= implode(', ', array_map(function($expression) {
            return $expression->getQuerySql();
        }, $this->getExpressions())) . "\n";
    }

    /**
     * Gets the type of expression that this clause contains
     * 
     * @return Type
     */
    public static function getExpressionInterfaceType(): Type {
        return Type::new(IOrderByExpression::class);
    }

    /**
     * Creates a new OrderBy instance
     * 
     * @param string|IColumnIdentifier|IOrderByExpression ...$expressions
     */
    public static function new(...$expressions): self {
        return new self(...$expressions);
    }
}

/**
 * An expression constructor that parses a string into an OrderByExpression
 */
OrderBy::registerExpressionConstructor(
    "ColumnIdentifier String",
    function($string): ?IOrderByExpression {
        if (!is_string($string)) {
            return null;
        }

        $pattern = PregPatterns::COLUMN_IDENTIFIER_PATTERN . "(?:\s+(asc|desc))?\s*\$";

        if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            return null;
        }

        $columnIdentifier = ColumnIdentifier::new($matches[3], $matches[2], $matches[1], null);
        $direction = $matches[4] ??= null;

        return new class($columnIdentifier, $direction) extends OrderByExpression implements IOrderByExpression {
            private IColumnIdentifier $columnIdentifier;
            private ?string $direction;

            public function __construct(IColumnIdentifier $columnIdentifier, ?string $direction = null) {
                $this->columnIdentifier = $columnIdentifier;
                $this->direction = $direction;
            }

            public function getQueryParameters(): array {
                return [];
            }

            public function getQuerySql(): string {
                return (string) $this->columnIdentifier . ($this->direction !== null ? " $this->direction" : '');
            }
        };
    }
, 0);

/**
 * An expression constructor that parses a ColumnIdentifier into an OrderByExpression
 */
OrderBy::registerExpressionConstructor(
    "ColumnIdentifier Object",
    function($columnIdentifier): ?IOrderByExpression {
        if (!($columnIdentifier instanceof ColumnIdentifier)) {
            return null;
        }

        $columnIdentifier = $columnIdentifier->getAlias() !== null ? 
            ColumnIdentifier::new($columnIdentifier->getSchemaName(), $columnIdentifier->getTableName(), $columnIdentifier->getColumnName()) :
            $columnIdentifier;

        return new class($columnIdentifier) extends CoreObject implements IOrderByExpression {
            private IColumnIdentifier $columnIdentifier;

            public function __construct(IColumnIdentifier $columnIdentifier) {
                $this->columnIdentifier = $columnIdentifier;
            }

            public function getQueryParameters(): array {
                return [];
            }

            public function getQuerySql(): string {
                return (string) $this->columnIdentifier;
            }
        };
    }
, 0);