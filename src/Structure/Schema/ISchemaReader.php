<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Schema;

use Pst\Core\Collections\IReadOnlyCollection;

use Pst\Database\Structure\Table\ITableReader;

interface ISchemaReader extends ITableReader {
    public function readSchemas(): IReadOnlyCollection;
    public function readSchema(string $schemaName): Schema;
}
