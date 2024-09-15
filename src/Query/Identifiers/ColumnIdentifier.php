<?php

declare(strict_types=1);

namespace Pst\Database\Query\Identifiers;

use Pst\Core\CoreObject;

use Pst\Database\Preg;

use InvalidArgumentException;

class ColumnIdentifier extends CoreObject implements IColumnIdentifier {
    private ?string $schemaName = null;
    private ?string $tableName = null;
    private string $columnName;
    private ?string $alias = null;

    private function sanitizeName(string $key, string $name): string {
        if (empty($name = trim($name))) {
            throw new InvalidArgumentException("Invalid name: '$name'");
        } else if ($name[0] === '`') {
            if (strlen($name) < 3 || $name[strlen($name) - 1] !== '`') {
                throw new InvalidArgumentException("Invalid name: '$name'");
            }

            $name = substr($name, 1, -1);
        }

        if (!preg_match("/^[a-zA-Z0-9_]+$/", $name)) {
            throw new InvalidArgumentException("Invalid $key: '$name'");
        }

        return $name;
    }

    private function __construct(?string $schemaName, ?string $tableName, string $columnName, ?string $aliasName) {
        if ($schemaName !== null) {
            $this->schemaName = $this->sanitizeName("schema name", $schemaName);
        }

        if ($tableName !== null) {
            $this->tableName = $this->sanitizeName("table name", $tableName);
        }

        $this->columnName = $this->sanitizeName("column name", $columnName);

        if ($aliasName !== null) {
            $this->alias = $this->sanitizeName("alias name", $aliasName);
        }
    }

    public function getSchemaName(): ?string {
        return $this->schemaName;
    }

    public function getTableName(): ?string {
        return $this->tableName;
    }

    public function getColumnName(): string {
        return $this->columnName;
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

    public function __toString(): string {
        return ($this->schemaName !== null ? "`{$this->schemaName}`." : "") . ($this->tableName !== null ? "`{$this->tableName}`." : "") . "`{$this->columnName}`" . (empty($this->alias) ? "" : " AS " . "`{$this->alias}`");
    }

    public static function tryParse(string $input): ?self {
        $Preg = Preg::COLUMN_IDENTIFIER_WITH_OPTIONAL_ALIAS_PATTERN;

        if (!preg_match("/^" . $Preg . "\s*$/i", $input, $matches)) {
            return null;
        }

        return static::new($matches[3], $matches[2], $matches[1], $matches[4] ?? null);
    }

    public static function new(string $columnName, ?string $tableName = null, ?string $schemaName = null, ?string $alias = null): IColumnIdentifier {
        return new ColumnIdentifier(...array_map(fn($match) => empty($match) ? null : trim(trim($match), '`'), [$schemaName, $tableName, $columnName, $alias]));
    }   
}