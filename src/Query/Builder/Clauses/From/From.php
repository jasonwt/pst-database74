<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\From;

use Pst\Core\Types\Type;

use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\Traits\ExpressionsTrait;
use Pst\Database\Query\Builder\Identifiers\TableIdentifier;

class From extends Clause implements IFrom {
    use ExpressionsTrait;

    public function getQuerySql(): string {
        return $this->querySql ??= implode(', ', array_map(function($expression) {
            return $expression->getQuerySql();
        }, $this->getExpressions())) . "\n";
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::fromTypeName(IFromExpression::class);
    }

    public static function new(...$expressions): self {
        return new self(...$expressions);
    }
}

/**
 * An expression constructor that parses a string into an FromExpression
 */
From::registerExpressionConstructor(
    "TableIdentifier String",
    function($string): ?IFromExpression {
        if (!is_string($string) || ($tableIdentifier = TableIdentifier::tryParse($string)) === null) {
            return null;
        }

        return new FromExpression($tableIdentifier);
    }
, 0);

/**
 * An expression constructor that parses a tableIdentifier into an FromByExpression
 */
From::registerExpressionConstructor(
    "TableIdentifier Object",
    function($tableIdentifier): ?IFromExpression {
        if (!($tableIdentifier instanceof TableIdentifier)) {
            return null;
        }

        return new FromExpression($tableIdentifier);
    }
, 0);