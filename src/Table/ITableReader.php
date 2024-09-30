<?php

declare(strict_types=1);

namespace Pst\Database\Table;

use Pst\Core\Enumerable\IRewindableEnumerable;

use Pst\Database\Index\IIndexReader;
use Pst\Database\Column\IColumnReader;

interface ITableReader extends IColumnReader, IIndexReader {
    public function readTable(string $schemaName, string $tableName): Table;
    public function readTables(string $schemaName): IRewindableEnumerable;
}
