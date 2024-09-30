<?php

declare(strict_types=1);

namespace Pst\Database\Connections;

use Pst\Core\Enumerable\IRewindableEnumerable;

interface ITableReader {
    public function readTables(?string $schemaName = null, ?string $tableName = null): IRewindableEnumerable;
}