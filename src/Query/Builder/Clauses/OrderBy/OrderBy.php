<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\OrderBy;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;

use Pst\Database\Preg;
use Pst\Database\Query\Identifiers\ColumnIdentifier;
use Pst\Database\Query\Identifiers\IColumnIdentifier;
use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;


class OrderBy extends Clause implements IOrderBy {
    use ClauseExpressionsTrait;


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

        $pattern = Preg::COLUMN_IDENTIFIER_PATTERN . "(?:\s+(asc|desc))?\s*\$";

        if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            return null;
        }

        $columnIdentifier = ColumnIdentifier::new($matches[3], $matches[2], $matches[1], null);
        $direction = $matches[4] ??= null;

        return new class($columnIdentifier, $direction) extends OrderByExpression implements IOrderByExpression {
            public function __construct(IColumnIdentifier $columnIdentifier, ?string $direction = null) {
                parent::__construct([$columnIdentifier, $direction]);
            }

            public function getQueryParameters(): array {
                return [];
            }

            public function getQuerySql(): string {
                list ($columnIdentifier, $direction) = $this->getExpression();
                return (string) $columnIdentifier . ($direction !== null ? " $direction" : '');
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

        return new class($columnIdentifier) extends OrderByExpression implements IOrderByExpression {
            private IColumnIdentifier $columnIdentifier;

            public function __construct(IColumnIdentifier $columnIdentifier) {
                parent::__construct($columnIdentifier);
            }

            public function getQueryParameters(): array {
                return [];
            }

            public function getQuerySql(): string {
                return (string) $this->getExpression();
            }
        };
    }
, 0);