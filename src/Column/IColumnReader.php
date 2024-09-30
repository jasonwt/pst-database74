<?php

declare(strict_types=1);

namespace Pst\Database\Column;

use Pst\Core\Enumerable\IRewindableEnumerable;

interface IColumnReader {
    public function readColumns(string $schemaName, string $tableName): IRewindableEnumerable;
    public function readColumn(string $schemaName, string $tableName, string $columnName): Column;
}
