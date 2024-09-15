<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Limit;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;
use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;

class Limit extends Clause implements ILimit {
    use ClauseExpressionsTrait;

    public function getQuerySql(): string {
        return $this->querySql ??= implode(', ', array_map(function($expression) {
            return $expression->getQuerySql();
        }, $this->getExpressions())) . "\n";
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::new(ILimitExpression::class);
    }

    public static function new($expression): self {
        return new self($expression);
    }
}

/**
 * An expression constructor that parses a tableIdentifier into an FromByExpression
 */
Limit::registerExpressionConstructor(
    "Numeric Value",
    function($intValue): ?ILimitExpression {
        if (is_string($intValue)) {
            if (!is_numeric($intValue) || strpos($intValue, '.') !== false) {
                return null;
            }

            $intValue = (int) $intValue;
        } else if (!is_int($intValue)) {
            return null;
        }

        if ($intValue < 1) {
            return null;
        }

        return new class($intValue) extends LimitExpression implements ILimitExpression {
            public function __construct($limit) {
                parent::__construct($limit);
            }

            public function getQuerySql(): string {
                return ":p" . $this->getObjectId();
            }

            public function getQueryParameters(): array {
                return ["p" . $this->getObjectId() => $this->getExpression()];
            }
        };
    }
, 0);