<?php

declare(strict_types=1);

namespace Pst\Database\Connections;

use Pst\Core\Enumerable\IRewindableEnumerable;

interface IIndexReader {
    public function readIndexes(?string $schemaName = null, ?string $tableName = null, ?string $indexName = null): IRewindableEnumerable;
}