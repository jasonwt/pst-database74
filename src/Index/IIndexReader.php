<?php

declare(strict_types=1);

namespace Pst\Database\Index;

use Pst\Core\Collections\IReadonlyCollection;

interface IIndexReader {
    public function readIndex(string $schemaName, string $tableName, string $indexName): Index;
    public function readIndexes(string $schemaName, string $tableName): IReadonlyCollection;
}
