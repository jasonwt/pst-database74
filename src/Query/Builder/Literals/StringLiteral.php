<?php

declare(strict_types=1);

namespace Pst\Database\Query\Builder\Literals;

use Pst\Core\CoreObject;

use InvalidArgumentException;
use Pst\Database\PregPatterns;

class StringLiteral extends CoreObject implements IStringLiteral {
    private string $value;
    private ?string $alias = null;

    private function __construct(string $value, ?string $alias = null) {
        if (empty($value = trim($value))) {
            throw new \InvalidArgumentException("Invalid string literal: '$value'");
        } else if (strlen($value) < 2) {
            throw new \InvalidArgumentException("Invalid string literal: '$value'");
        } else if ($value[0] !== "'" && $value[0] !== '"') {
            throw new \InvalidArgumentException("Invalid string literal: '$value'");
        } else if ($value[0] !== $value[strlen($value) - 1]) {
            throw new \InvalidArgumentException("Invalid string literal: '$value'");
        }

        $value = str_replace("\\" . $value[0], "\\'", $value);

        $this->value = substr($value, 1, -1);
        $this->alias = $alias;
    }

    public function getValue(): string {
        return $this->value;
    }

    public function getAlias(): ?string {
        return $this->alias;
    }

    public function getQuerySql(): string {
        return (string) $this;
    }

    public function getQueryParameters(): array {
        return [];
    }

    public static function tryParse(string $value): ?StringLiteral {
        $pattern = PregPatterns::STRING_LITERAL_PATTERN;

        if (!preg_match('/^' . $pattern . '/s*\$/i', $value, $matches)) {
            return null;
        }

        print_r($matches);
        exit;

        return new StringLiteral($value);
    }

    public static function new(string $value, ?string $alias = null): StringLiteral {
        return new StringLiteral($value, $alias);
    }

    public function __toString(): string {
        return "'" . $this->value . "'" . ($this->alias !== null ? " AS " . $this->alias : "");
    }
}