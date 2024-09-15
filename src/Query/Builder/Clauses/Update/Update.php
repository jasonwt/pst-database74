<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Update;

use Pst\Core\Types\Type;

use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;
use Pst\Database\Query\Identifiers\TableIdentifier;

class Update extends Clause implements IUpdate {
    use ClauseExpressionsTrait;

    public function getQuerySql(): string {
        return $this->querySql ??= implode(', ', array_map(function($expression) {
            return $expression->getQuerySql();
        }, $this->getExpressions())) . "\n";
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::new(IUpdateExpression::class);
    }

    public static function new(...$expressions): self {
        return new self(...$expressions);
    }
}

/**
 * An expression constructor that parses a string into an UpdateExpression
 */
Update::registerExpressionConstructor(
    "TableIdentifier String",
    function($string): ?IUpdateExpression {
        if (!is_string($string) || ($tableIdentifier = TableIdentifier::tryParse($string)) === null) {
            return null;
        }

        return new UpdateExpression($tableIdentifier);
    }
, 0);

/**
 * An expression constructor that parses a tableIdentifier into an UpdateByExpression
 */
Update::registerExpressionConstructor(
    "TableIdentifier Object",
    function($tableIdentifier): ?IUpdateExpression {
        if (!($tableIdentifier instanceof TableIdentifier)) {
            return null;
        }

        return new UpdateExpression($tableIdentifier);
    }
, 0);