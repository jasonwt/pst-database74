<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\ReplaceInto;

use Pst\Core\Types\Type;

use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;
use Pst\Database\Query\Identifiers\TableIdentifier;

class ReplaceInto extends Clause implements IReplaceInto {
    use ClauseExpressionsTrait;

    public function getQuerySql(): string {
        return $this->getExpressions()[0]->getQuerySql() . "\n";
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::new(IReplaceIntoExpression::class);
    }

    public static function new($tableExpression): self {
        return new self($tableExpression);
    }
}

/**
 * An expression constructor that parses a string into an ReplaceIntoExpression
 */
ReplaceInto::registerExpressionConstructor(
    "TableIdentifier String",
    function($string): ?IReplaceIntoExpression {
        if (!is_string($string) || ($tableIdentifier = TableIdentifier::tryParse($string)) === null) {
            return null;
        }

        return new ReplaceIntoExpression($tableIdentifier);
    }
, 0);

/**
 * An expression constructor that parses a tableIdentifier into an ReplaceIntoByExpression
 */
ReplaceInto::registerExpressionConstructor(
    "TableIdentifier Object",
    function($tableIdentifier): ?IReplaceIntoExpression {
        if (!($tableIdentifier instanceof TableIdentifier)) {
            return null;
        }

        return new ReplaceIntoExpression($tableIdentifier);
    }
, 0);