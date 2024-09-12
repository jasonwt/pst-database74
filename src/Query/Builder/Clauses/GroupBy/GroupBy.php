<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\GroupBy;

use Pst\Core\Types\Type;

use Pst\Database\PregPatterns;
use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Identifiers\ColumnIdentifier;
use Pst\Database\Query\Builder\Clauses\Traits\ExpressionsTrait;

class GroupBy extends Clause implements IGroupBy {
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
        return Type::new(IGroupByExpression::class);
    }

    /**
     * Creates a new GroupBy instance
     * 
     * @param string|IColumnIdentifier|IGroupByExpression ...$expressions
     */
    public static function new(...$expressions): self {
        return new self(...$expressions);
    }
}

/**
 * An expression constructor that parses a string into an GroupByExpression
 */
GroupBy::registerExpressionConstructor(
    "ColumnIdentifier String",
    function($string): ?IGroupByExpression {
        if (!is_string($string)) {
            return null;
        }

        $pattern = PregPatterns::COLUMN_IDENTIFIER_PATTERN . "(?:\s+(asc|desc))?\s*\$";

        if (!preg_match("/^" . $pattern . "\s*\$/i", $string, $matches)) {
            return null;
        }

        $columnIdentifier = ColumnIdentifier::new($matches[3], $matches[2], $matches[1], null);
        $direction = $matches[4] ??= null;

        return new GroupByExpression($columnIdentifier, $direction);
    }
, 0);

/**
 * An expression constructor that parses a ColumnIdentifier into an GroupByExpression
 */
GroupBy::registerExpressionConstructor(
    "ColumnIdentifier Object",
    function($columnIdentifier): ?IGroupByExpression {
        if (!($columnIdentifier instanceof ColumnIdentifier)) {
            return null;
        }

        return new GroupByExpression(ColumnIdentifier::new($columnIdentifier->getSchemaName(), $columnIdentifier->getTableName(), $columnIdentifier->getColumnName(), null));
    }
, 0);