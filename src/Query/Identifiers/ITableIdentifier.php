<?php

declare(strict_types=1);

namespace Pst\Database\Query\Identifiers;

interface ITableIdentifier extends IIdentifier {
    public function getSchemaName(): ?string;
    public function getTableName(): string;

    public static function new(string $tableName, ?string $schemaName = null, ?string $alias = null): ITableIdentifier;
}