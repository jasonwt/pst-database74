<?php

declare(strict_types=1);

namespace Pst\Database\Column;

abstract class ColumnReader implements IColumnReader {
    use ColumnReaderTrait;
}