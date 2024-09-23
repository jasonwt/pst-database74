<?php

declare(strict_types=1);

namespace Pst\Database\Schema;

use Pst\Database\Table\TableReaderTrait;

abstract class SchemaReader implements ISchemaReader {
    use TableReaderTrait;
}