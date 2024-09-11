<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Readers;

use Pst\Core\Collections\IEnumerable;

use Pst\Database\Structure\Column;

interface IColumnReader {
    public function ReadColumns(string $schemaName, string $tableName): IEnumerable;
    public function ReadColumn(string $schemaName, string $tableName, string $columnName): Column;
}
