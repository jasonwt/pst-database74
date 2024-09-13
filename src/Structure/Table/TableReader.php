<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Table;

use Pst\Database\Structure\Column\ColumnReaderTrait;
use Pst\Database\Structure\Index\IndexReaderTrait;

abstract class IIndexReader implements ITableReader {
    use ColumnReaderTrait;
    use IndexReaderTrait;
}