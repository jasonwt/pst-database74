<?php

declare(strict_types=1);

namespace Pst\Database\Table;

use Pst\Database\Column\ColumnReaderTrait;
use Pst\Database\Index\IndexReaderTrait;

abstract class IIndexReader implements ITableReader {
    use ColumnReaderTrait;
    use IndexReaderTrait;
}