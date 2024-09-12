<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Literals;

use Pst\Core\CoreObject;

use InvalidArgumentException;

class NumericLiteral extends CoreObject implements INumericLiteral {
    private float $value;

    private function __construct(float $value) {
        $this->value = $value;
    }

    public function getValue(): float {
        return $this->value;
    }

    public function getQuerySql(): string {
        return ":p" . $this->getObjectId();
        //return (string) $this;
    }

    public function getQueryParameters(): array {
        return ["p" . $this->getObjectId() => $this->value];
    }

    public static function tryParse(string $value): ?NumericLiteral {
        if (empty($value = trim($value))) {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return new NumericLiteral((float) $value);
    }

    public static function new(float $value): NumericLiteral {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException("Invalid numeric literal: '$value'");
        }

        return new NumericLiteral((float) $value);
    }

    public function __toString(): string {
        throw new \Exception("Not implemented");
        echo "ASDFASDF";
        return (string)$this->value;
    }
}