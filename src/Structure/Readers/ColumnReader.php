<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Readers;

use Pst\Database\Structure\Readers\Traits\ColumnReaderTrait;

abstract class ColumnReader implements IColumnReader {
    use ColumnReaderTrait;
}