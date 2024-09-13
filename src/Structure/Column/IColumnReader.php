<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Column;

use Pst\Core\Collections\IEnumerable;

interface IColumnReader {
    public function ReadColumns(string $schemaName, string $tableName): IEnumerable;
    public function ReadColumn(string $schemaName, string $tableName, string $columnName): Column;
}
