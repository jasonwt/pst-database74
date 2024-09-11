<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Readers;

use Pst\Core\Collections\IEnumerable;

use Pst\Database\Structure\Index;

interface IIndexReader {
    public function ReadIndexes(string $schemaName, string $tableName): IEnumerable;
    public function ReadIndex(string $schemaName, string $tableName, string $indexName): Index;
}
