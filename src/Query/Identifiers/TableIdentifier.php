<?php

declare(strict_types=1);

namespace Pst\Database\Query\Identifiers;

use Pst\Core\CoreObject;

use Pst\Database\Preg;

use InvalidArgumentException;

class TableIdentifier extends CoreObject implements ITableIdentifier {
    private ?string $schemaName = null;
    private string $tableName;
    private ?string $alias = null;

    private function __construct(?string $schemaName, string $tableName, ?string $alias) {
        if ($schemaName !== null && !preg_match("/^[a-zA-Z0-9_]+$/", $this->schemaName = trim($schemaName))) {
            throw new InvalidArgumentException("Invalid schema name: '$schemaName'");
        }

        if (!preg_match("/^[a-zA-Z0-9_]+$/", $this->tableName = trim($tableName))) {
            throw new InvalidArgumentException("Invalid table name: '$tableName'");
        }

        if ($alias !== null && !preg_match("/^[a-zA-Z0-9_]+$/", $this->alias = trim($alias))) {
            throw new InvalidArgumentException("Invalid alias name: '$alias'");
        }
    }

    public function getSchemaName(): ?string {
        return $this->schemaName;
    }

    public function getTableName(): string {
        return $this->tableName;
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
        return ($this->schemaName !== null ? "`{$this->schemaName}`." : "") . "`{$this->tableName}`" . (empty($this->alias) ? "" : " AS " . "`{$this->alias}`");
    }

    public static function tryParse(string $input): ?self {
        $Preg = Preg::TABLE_IDENTIFIER_WITH_OPTIONAL_ALIAS_PATTERN;

        if (!preg_match("/^$Preg$/i", $input, $matches)) {
            return null;
        }

        $matches = array_map(fn($match) => empty($match) ? null : trim(trim($match), '`'), $matches);
        return static::new($matches[2], $matches[1], $matches[3] ?? null);
    }

    public static function new(string $tableName, ?string $schemaName = null, ?string $alias = null): ITableIdentifier {
        return new TableIdentifier(...array_map(fn($match) => empty($match) ? null : trim(trim($match), '`'), [$schemaName, $tableName, $alias]));
    }
}