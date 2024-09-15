<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\DeleteFrom;

use Pst\Core\Types\Type;

use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;
use Pst\Database\Query\Identifiers\TableIdentifier;

class DeleteFrom extends Clause implements IDeleteFrom {
    use ClauseExpressionsTrait;

    public function getQuerySql(): string {
        return $this->querySql ??= implode(', ', array_map(function($expression) {
            return $expression->getQuerySql();
        }, $this->getExpressions())) . "\n";
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::new(IDeleteFromExpression::class);
    }

    public static function new(...$expressions): self {
        return new self(...$expressions);
    }
}

/**
 * An expression constructor that parses a string into an DeleteFromExpression
 */
DeleteFrom::registerExpressionConstructor(
    "TableIdentifier String",
    function($string): ?IDeleteFromExpression {
        if (!is_string($string) || ($tableIdentifier = TableIdentifier::tryParse($string)) === null) {
            return null;
        }

        return new DeleteFromExpression($tableIdentifier);
    }
, 0);

/**
 * An expression constructor that parses a tableIdentifier into an DeleteFromByExpression
 */
DeleteFrom::registerExpressionConstructor(
    "TableIdentifier Object",
    function($tableIdentifier): ?IDeleteFromExpression {
        if (!($tableIdentifier instanceof TableIdentifier)) {
            return null;
        }

        return new DeleteFromExpression($tableIdentifier);
    }
, 0);