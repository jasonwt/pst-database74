<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Readers;

use Pst\Database\Structure\Readers\Traits\TableReaderTrait;

abstract class SchemaReader implements ISchemaReader {
    use TableReaderTrait;
}