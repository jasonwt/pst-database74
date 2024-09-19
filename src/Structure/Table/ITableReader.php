<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Table;

use Pst\Core\Collections\IReadOnlyCollection;

use Pst\Database\Structure\Column\IColumnReader;
use Pst\Database\Structure\Index\IIndexReader;

interface ITableReader extends IColumnReader, IIndexReader {
    public function readTable(string $schemaName, string $tableName): Table;
    public function readTables(string $schemaName): IReadOnlyCollection;
}
