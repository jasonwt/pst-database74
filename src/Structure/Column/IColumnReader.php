<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Column;

use Pst\Core\Collections\IReadOnlyCollection;

interface IColumnReader {
    public function readColumns(string $schemaName, string $tableName): IReadOnlyCollection;
    public function readColumn(string $schemaName, string $tableName, string $columnName): Column;
}
