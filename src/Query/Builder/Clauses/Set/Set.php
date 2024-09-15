<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Set;

use LogicException;
use Pst\Core\Types\Type;

use Pst\Database\Preg;
use Pst\Database\Query\Identifiers\ColumnIdentifier;
use Pst\Database\Query\Identifiers\IColumnIdentifier;
use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;
use Pst\Database\Query\Literals\NumericLiteral;
use Pst\Database\Query\Literals\StringLiteral;

class Set extends Clause implements ISet {
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
        return Type::new(ISetExpression::class);
    }

    /**
     * Creates a new Set instance
     * 
     * @param string|IColumnIdentifier|ISetExpression ...$expressions
     */
    public static function new(...$expressions): self {
        return new self(...$expressions);
    }
}

/**
 * An expression constructor that parses a string into an SetExpression
 */
Set::registerExpressionConstructor(
    "Parse Simple String",
    function($string): ?ISetExpression {
        if (!is_string($string)) {
            return null;
        }

        $columnIdentifierPattern = "/^(" . Preg::COLUMN_IDENTIFIER_PATTERN . ")\s*(?:=)\s*(.*)\$/i";

        if (!preg_match($columnIdentifierPattern, $string, $matches)) {
            return null;
        }

        if (($leftColumnIdentifier = ColumnIdentifier::new($matches[4], $matches[3], $matches[2])) !== null) {
            $string = $matches[5];
        } else {
            throw new LogicException("Invalid column identifier: $matches[1]");
        }

        $rightAssignmentValue = null;
        
        if (!preg_match("/^(" . Preg::COLUMN_IDENTIFIER_PATTERN . ")\s*\$/i", $string, $matches)) {
            if (!preg_match("/^(" . Preg::NUMERIC_PATTERN . ")\s*\$/i", $string, $matches)) {
                if (!preg_match("/^(" . Preg::SINGLE_QUOTED_STRING_PATTERN . "|" . Preg::DOUBLE_QUOTED_STRING_PATTERN . ")\s*\$/i", $string, $matches)) {

                    return null;
                } else {
                    if (($rightAssignmentValue = StringLiteral::tryParse($matches[1])) === null) {
                        throw new LogicException("Invalid string literal: $matches[1]");
                    }
                }
            } else {
                if (($rightAssignmentValue = NumericLiteral::tryParse($matches[1])) === null) {
                    throw new LogicException("Invalid numeric literal: $matches[1]");
                }
            }
        } else {
            $rightAssignmentValue = ColumnIdentifier::new($matches[4], $matches[3], $matches[2]);
        }

        return new class($leftColumnIdentifier, $rightAssignmentValue) extends SetExpression implements ISetExpression {
            public function __construct(IColumnIdentifier $leftColumnIdentifier, $rightAssignmentValue) {
                parent::__construct([$leftColumnIdentifier, $rightAssignmentValue]);
            }

            public function getQueryParameters(): array {
                return [];
            }

            public function getQuerySql(): string {
                list ($leftColumnIdentifier, $rightAssignmentValue) = $this->getExpression();
                return (string) $leftColumnIdentifier . " = " . (string) $rightAssignmentValue;
            }
        };
    }
, 0);