<?php

declare(strict_types=1);

namespace Pst\Database\Connections;

use Pst\Core\Enumerable\IRewindableEnumerable;

interface IColumnReader {
    public function readColumns(?string $schemaName = null, ?string $tableName = null, ?string $columnName = null): IRewindableEnumerable;
}