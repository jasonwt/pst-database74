<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Index;

use Pst\Core\Collections\IReadOnlyCollection;

interface IIndexReader {
    public function readIndex(string $schemaName, string $tableName, string $indexName): Index;
    public function readIndexes(string $schemaName, string $tableName): IReadOnlyCollection;
}
