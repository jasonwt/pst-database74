<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Clauses\Offset;

use Pst\Core\CoreObject;
use Pst\Core\Types\Type;
use Pst\Database\Query\Builder\Clauses\Clause;
use Pst\Database\Query\Builder\Clauses\ClauseExpressionsTrait;

class Offset extends Clause implements IOffset {
    use ClauseExpressionsTrait;

    public function getQuerySql(): string {
        return $this->querySql ??= implode(', ', array_map(function($expression) {
            return $expression->getQuerySql();
        }, $this->getExpressions())) . "\n";
    }

    public static function getExpressionInterfaceType(): Type {
        return Type::new(IOffsetExpression::class);
    }

    public static function new($expression): self {
        return new self($expression);
    }
}

/**
 * An expression constructor that parses a tableIdentifier into an FromByExpression
 */
Offset::registerExpressionConstructor(
    "Numeric Value",
    function($intValue): ?IOffsetExpression {
        if (is_string($intValue)) {
            if (!is_numeric($intValue) || strpos($intValue, '.') !== false) {
                return null;
            }

            $intValue = (int) $intValue;
        } else if (!is_int($intValue)) {
            return null;
        }

        if ($intValue < 0) {
            return null;
        }

        return new class($intValue) extends OffsetExpression implements IOffsetExpression {
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