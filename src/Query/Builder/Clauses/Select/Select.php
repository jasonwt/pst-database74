<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Select;

use Pst\Core\Types\Type;

use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;
use Pst\Database\Query\Identifiers\ColumnIdentifier;
use Pst\Database\Query\Literals\NumericLiteral;
use Pst\Database\Query\Literals\StringLiteral;

class Select extends Clause implements ISelect {
    use ClauseExpressionsTrait;

    public function getQuerySql(): string {
        return $this->querySql ??= implode(', ', array_map(function($expression) {
            return $expression->getQuerySql();
        }, $this->getExpressions())) . "\n";
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::new(ISelectExpression::class);
    }

    public static function new(...$expressions): self {
        return new self(...$expressions);
    }
}

/**
 * An expression constructor that parses a string into an SelectExpression
 */
Select::registerExpressionConstructor(
    "String",
    function($string): ?ISelectExpression {
        if (is_string($string)) {
            if (($columnIdentifier = ColumnIdentifier::tryParse($string)) !== null) {
                return new SelectExpression($columnIdentifier);
            } else if (($stringLiteral = StringLiteral::tryParse($string)) !== null) {
                return new SelectExpression($stringLiteral);
            } else if (($numericLiteral = NumericLiteral::tryParse($string)) !== null) {
                return new SelectExpression($numericLiteral);
            }

        }
     
        return null;
    }
, 0);

/**
 * An expression constructor that parses a ColumnIdentifier into an SelectByExpression
 */
Select::registerExpressionConstructor(
    "ColumnIdentifier Object",
    function($columnIdentifier): ?ISelectExpression {
        if (!($columnIdentifier instanceof ColumnIdentifier)) {
            return null;
        }

        return new SelectExpression($columnIdentifier);
    }
, 0);

