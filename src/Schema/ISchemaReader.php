<?php

declare(strict_types=1);

namespace Pst\Database\Schema;

use Pst\Core\Collections\IReadonlyCollection;

use Pst\Database\Table\ITableReader;

interface ISchemaReader extends ITableReader {
    public function readSchemas(): IReadonlyCollection;
    public function readSchema(string $schemaName): Schema;
}
