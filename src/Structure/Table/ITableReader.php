<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Table;

use Pst\Core\Collections\IEnumerable;

use Pst\Database\Structure\Column\IColumnReader;

interface ITableReader extends IColumnReader, IIndexReader {
    public function readTables(string $schemaName): IEnumerable;
    public function readTable(string $schemaName, string $tableName): Table;
}
