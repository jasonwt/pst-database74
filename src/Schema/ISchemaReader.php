<?php

declare(strict_types=1);

namespace Pst\Database\Schema;

use Pst\Core\Enumerable\IRewindableEnumerable;

use Pst\Database\Table\ITableReader;

interface ISchemaReader extends ITableReader {
    public function readSchemas(): IRewindableEnumerable;
    public function readSchema(string $schemaName): Schema;
}
