<?php

declare(strict_types=1);

namespace Pst\Database\Connections;

use Pst\Core\Enumerable\IRewindableEnumerable;

interface ISchemaReader {
    public function readSchemas(?string $schemaName = null): IRewindableEnumerable;
}