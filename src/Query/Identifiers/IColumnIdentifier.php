<?php

declare(strict_types=1);

namespace Pst\Database\Query\Identifiers;

interface IColumnIdentifier extends IIdentifier {
    public function getSchemaName(): ?string;
    public function getTableName(): ?string;
    public function getColumnName(): string;

    public static function new(string $columnName, ?string $tableName = null, ?string $schemaName = null, ?string $alias = null): IColumnIdentifier;
}