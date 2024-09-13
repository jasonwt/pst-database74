<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Schema;

use Pst\Core\Collections\IEnumerable;

use Pst\Database\Structure\Table\ITableReader;

interface ISchemaReader extends ITableReader {
    public function ReadSchemas(): IEnumerable;
    public function ReadSchema(string $schemaName): Schema;
}
