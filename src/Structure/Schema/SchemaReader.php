<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Schema;

use Pst\Database\Structure\Table\TableReaderTrait;

abstract class SchemaReader implements ISchemaReader {
    use TableReaderTrait;
}