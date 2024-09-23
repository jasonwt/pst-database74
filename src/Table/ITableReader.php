<?php

declare(strict_types=1);

namespace Pst\Database\Table;

use Pst\Core\Collections\IReadonlyCollection;

use Pst\Database\Column\IColumnReader;
use Pst\Database\Index\IIndexReader;

interface ITableReader extends IColumnReader, IIndexReader {
    public function readTable(string $schemaName, string $tableName): Table;
    public function readTables(string $schemaName): IReadonlyCollection;
}
