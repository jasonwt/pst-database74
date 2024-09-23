<?php

declare(strict_types=1);

namespace Pst\Database\Column;

use Pst\Core\Collections\IReadonlyCollection;

interface IColumnReader {
    public function readColumns(string $schemaName, string $tableName): IReadonlyCollection;
    public function readColumn(string $schemaName, string $tableName, string $columnName): Column;
}
