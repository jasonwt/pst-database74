<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Readers;

use Pst\Core\Collections\IEnumerable;

use Pst\Database\Structure\Schema;

interface ISchemaReader extends ITableReader{
    public function ReadSchemas(): IEnumerable;
    public function ReadSchema(string $schemaName): Schema;
}
