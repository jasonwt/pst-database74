<?php

declare(strict_types=1);

namespace Pst\Database\Structure\Readers;

use Pst\Database\Structure\Readers\Traits\IndexReaderTrait;

abstract class IIndexReader implements IIndexReader {
    use IndexReaderTrait;
}